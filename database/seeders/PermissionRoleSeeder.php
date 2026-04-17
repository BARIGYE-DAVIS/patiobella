<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Default restaurant permissions (rights)
        $permissions = [
            // Users Management
            ['code' => 'view_users', 'name' => 'View Users', 'group' => 'users', 'sort_order' => 1],
            ['code' => 'create_users', 'name' => 'Create Users', 'group' => 'users', 'sort_order' => 2],
            ['code' => 'edit_users', 'name' => 'Edit Users', 'group' => 'users', 'sort_order' => 3],
            ['code' => 'delete_users', 'name' => 'Delete Users', 'group' => 'users', 'sort_order' => 4],
            ['code' => 'assign_roles', 'name' => 'Assign Roles to Users', 'group' => 'users', 'sort_order' => 5],
            
            // Roles Management
            ['code' => 'view_roles', 'name' => 'View Roles', 'group' => 'roles', 'sort_order' => 6],
            ['code' => 'create_roles', 'name' => 'Create Roles', 'group' => 'roles', 'sort_order' => 7],
            ['code' => 'edit_roles', 'name' => 'Edit Roles', 'group' => 'roles', 'sort_order' => 8],
            ['code' => 'delete_roles', 'name' => 'Delete Roles', 'group' => 'roles', 'sort_order' => 9],
            
            // Permissions Management
            ['code' => 'view_permissions', 'name' => 'View Permissions', 'group' => 'permissions', 'sort_order' => 10],
            ['code' => 'create_permissions', 'name' => 'Create Permissions', 'group' => 'permissions', 'sort_order' => 11],
            ['code' => 'edit_permissions', 'name' => 'Edit Permissions', 'group' => 'permissions', 'sort_order' => 12],
            ['code' => 'delete_permissions', 'name' => 'Delete Permissions', 'group' => 'permissions', 'sort_order' => 13],
            
            // Inventory Management
            ['code' => 'view_inventory', 'name' => 'View Inventory', 'group' => 'inventory', 'sort_order' => 20],
            ['code' => 'create_inventory', 'name' => 'Add Inventory Items', 'group' => 'inventory', 'sort_order' => 21],
            ['code' => 'edit_inventory', 'name' => 'Edit Inventory Items', 'group' => 'inventory', 'sort_order' => 22],
            ['code' => 'delete_inventory', 'name' => 'Delete Inventory Items', 'group' => 'inventory', 'sort_order' => 23],
            ['code' => 'adjust_inventory', 'name' => 'Adjust Stock (Counts)', 'group' => 'inventory', 'sort_order' => 24],
            ['code' => 'transfer_inventory', 'name' => 'Transfer Stock Between Stores', 'group' => 'inventory', 'sort_order' => 25],
            
            // Purchasing (PO)
            ['code' => 'view_purchase_orders', 'name' => 'View Purchase Orders', 'group' => 'purchasing', 'sort_order' => 30],
            ['code' => 'create_purchase_orders', 'name' => 'Create Purchase Orders', 'group' => 'purchasing', 'sort_order' => 31],
            ['code' => 'edit_purchase_orders', 'name' => 'Edit Purchase Orders', 'group' => 'purchasing', 'sort_order' => 32],
            ['code' => 'approve_purchase_orders', 'name' => 'Approve Purchase Orders', 'group' => 'purchasing', 'sort_order' => 33],
            ['code' => 'receive_goods', 'name' => 'Receive Goods (GRN)', 'group' => 'purchasing', 'sort_order' => 34],
            
            // Vendors
            ['code' => 'view_vendors', 'name' => 'View Vendors', 'group' => 'vendors', 'sort_order' => 40],
            ['code' => 'create_vendors', 'name' => 'Create Vendors', 'group' => 'vendors', 'sort_order' => 41],
            ['code' => 'edit_vendors', 'name' => 'Edit Vendors', 'group' => 'vendors', 'sort_order' => 42],
            ['code' => 'delete_vendors', 'name' => 'Delete Vendors', 'group' => 'vendors', 'sort_order' => 43],
            
            // Departments & Stores
            ['code' => 'view_departments', 'name' => 'View Departments', 'group' => 'departments', 'sort_order' => 50],
            ['code' => 'manage_departments', 'name' => 'Manage Departments (CRUD)', 'group' => 'departments', 'sort_order' => 51],
            ['code' => 'view_stores', 'name' => 'View Stores', 'group' => 'stores', 'sort_order' => 52],
            ['code' => 'manage_stores', 'name' => 'Manage Stores (CRUD)', 'group' => 'stores', 'sort_order' => 53],
            
            // Categories
            ['code' => 'view_categories', 'name' => 'View Categories', 'group' => 'categories', 'sort_order' => 60],
            ['code' => 'manage_categories', 'name' => 'Manage Categories (CRUD)', 'group' => 'categories', 'sort_order' => 61],
            
            // Reports
            ['code' => 'view_reports', 'name' => 'View Reports Dashboard', 'group' => 'reports', 'sort_order' => 70],
            ['code' => 'view_stock_reports', 'name' => 'View Stock Reports', 'group' => 'reports', 'sort_order' => 71],
            ['code' => 'view_financial_reports', 'name' => 'View Financial Reports', 'group' => 'reports', 'sort_order' => 72],
            ['code' => 'export_reports', 'name' => 'Export Reports', 'group' => 'reports', 'sort_order' => 73],
            
            // POS / Sales
            ['code' => 'access_pos', 'name' => 'Access POS System', 'group' => 'pos', 'sort_order' => 80],
            ['code' => 'create_sales', 'name' => 'Create Sales Orders', 'group' => 'pos', 'sort_order' => 81],
            ['code' => 'view_sales_history', 'name' => 'View Sales History', 'group' => 'pos', 'sort_order' => 82],
            ['code' => 'refund_sales', 'name' => 'Refund/Cancel Sales', 'group' => 'pos', 'sort_order' => 83],
            
            // Kitchen/Production
            ['code' => 'view_kitchen', 'name' => 'View Kitchen Orders', 'group' => 'kitchen', 'sort_order' => 90],
            ['code' => 'update_kitchen_status', 'name' => 'Update Order Status', 'group' => 'kitchen', 'sort_order' => 91],
            
            // Settings
            ['code' => 'view_settings', 'name' => 'View System Settings', 'group' => 'settings', 'sort_order' => 100],
            ['code' => 'edit_settings', 'name' => 'Edit System Settings', 'group' => 'settings', 'sort_order' => 101],
            
            // Audit Logs
            ['code' => 'view_audit_logs', 'name' => 'View Audit Logs', 'group' => 'audit', 'sort_order' => 110],
        ];

        // Insert permissions if they don't exist
        foreach ($permissions as $perm) {
            Permission::firstOrCreate(
                ['code' => $perm['code']],
                $perm
            );
        }

        // Create default roles if they don't exist
        $roles = [
            [
                'code' => 'super_admin',
                'name' => 'Super Administrator',
                'description' => 'Full system access with all permissions',
                'is_system_role' => true,
            ],
            [
                'code' => 'owner',
                'name' => 'Owner',
                'description' => 'Restaurant owner with all business permissions',
                'is_system_role' => true,
            ],
            [
                'code' => 'general_manager',
                'name' => 'General Manager',
                'description' => 'Manages overall restaurant operations',
                'is_system_role' => true,
            ],
            [
                'code' => 'kitchen_manager',
                'name' => 'Kitchen Manager',
                'description' => 'Manages kitchen operations and inventory',
                'is_system_role' => true,
            ],
            [
                'code' => 'bar_manager',
                'name' => 'Bar Manager',
                'description' => 'Manages bar operations and beverage inventory',
                'is_system_role' => true,
            ],
            [
                'code' => 'procurement_officer',
                'name' => 'Procurement Officer',
                'description' => 'Handles purchasing and vendor management',
                'is_system_role' => true,
            ],
            [
                'code' => 'store_keeper',
                'name' => 'Store Keeper',
                'description' => 'Manages inventory and stock movements',
                'is_system_role' => true,
            ],
            [
                'code' => 'cashier',
                'name' => 'Cashier',
                'description' => 'Handles POS and sales transactions',
                'is_system_role' => true,
            ],
            [
                'code' => 'waiter',
                'name' => 'Waiter/Waitress',
                'description' => 'Takes orders and serves customers',
                'is_system_role' => true,
            ],
            [
                'code' => 'chef',
                'name' => 'Chef',
                'description' => 'Prepares food and updates kitchen status',
                'is_system_role' => true,
            ],
            [
                'code' => 'accountant',
                'name' => 'Accountant',
                'description' => 'Handles financial reports and transactions',
                'is_system_role' => true,
            ],
            [
                'code' => 'viewer',
                'name' => 'Viewer (Read Only)',
                'description' => 'Can only view data, no modifications',
                'is_system_role' => true,
            ],
        ];

        foreach ($roles as $roleData) {
            Role::firstOrCreate(
                ['code' => $roleData['code']],
                $roleData
            );
        }

        // Assign permissions to roles (only if not already assigned)
        
        // Super Admin (all permissions)
        $superAdmin = Role::where('code', 'super_admin')->first();
        if ($superAdmin && $superAdmin->permissions()->count() === 0) {
            $allPermissions = Permission::where('is_active', true)->get();
            $superAdmin->permissions()->attach($allPermissions->pluck('id'));
        }

        // Owner (all permissions except system admin)
        $owner = Role::where('code', 'owner')->first();
        if ($owner && $owner->permissions()->count() === 0) {
            $ownerPermissions = Permission::whereNotIn('code', [
                'view_roles', 'create_roles', 'edit_roles', 'delete_roles',
                'view_permissions', 'create_permissions', 'edit_permissions', 'delete_permissions'
            ])->get();
            $owner->permissions()->attach($ownerPermissions->pluck('id'));
        }

        // General Manager
        $gm = Role::where('code', 'general_manager')->first();
        if ($gm && $gm->permissions()->count() === 0) {
            $gmPermissions = Permission::whereIn('group', [
                'inventory', 'purchasing', 'vendors', 'departments', 'stores',
                'categories', 'reports', 'pos', 'kitchen'
            ])->get();
            $gm->permissions()->attach($gmPermissions->pluck('id'));
        }

        // Kitchen Manager
        $kitchenMgr = Role::where('code', 'kitchen_manager')->first();
        if ($kitchenMgr && $kitchenMgr->permissions()->count() === 0) {
            $kitchenPermissions = Permission::whereIn('code', [
                'view_inventory', 'adjust_inventory', 'view_purchase_orders',
                'view_departments', 'view_reports', 'view_stock_reports',
                'view_kitchen', 'update_kitchen_status'
            ])->get();
            $kitchenMgr->permissions()->attach($kitchenPermissions->pluck('id'));
        }

        // Bar Manager
        $barMgr = Role::where('code', 'bar_manager')->first();
        if ($barMgr && $barMgr->permissions()->count() === 0) {
            $barPermissions = Permission::whereIn('code', [
                'view_inventory', 'adjust_inventory', 'view_purchase_orders',
                'view_vendors', 'view_departments', 'view_reports',
                'view_stock_reports', 'access_pos'
            ])->get();
            $barMgr->permissions()->attach($barPermissions->pluck('id'));
        }

        // Procurement Officer
        $procurement = Role::where('code', 'procurement_officer')->first();
        if ($procurement && $procurement->permissions()->count() === 0) {
            $procurementPermissions = Permission::whereIn('group', ['purchasing', 'vendors'])->get();
            $procurement->permissions()->attach($procurementPermissions->pluck('id'));
        }

        // Store Keeper
        $storeKeeper = Role::where('code', 'store_keeper')->first();
        if ($storeKeeper && $storeKeeper->permissions()->count() === 0) {
            $storePermissions = Permission::whereIn('code', [
                'view_inventory', 'create_inventory', 'edit_inventory',
                'adjust_inventory', 'transfer_inventory', 'view_purchase_orders',
                'receive_goods', 'view_vendors', 'view_departments',
                'view_stores', 'view_reports', 'view_stock_reports'
            ])->get();
            $storeKeeper->permissions()->attach($storePermissions->pluck('id'));
        }

        // Cashier
        $cashier = Role::where('code', 'cashier')->first();
        if ($cashier && $cashier->permissions()->count() === 0) {
            $cashierPermissions = Permission::whereIn('code', [
                'access_pos', 'create_sales', 'view_sales_history'
            ])->get();
            $cashier->permissions()->attach($cashierPermissions->pluck('id'));
        }

        // Waiter
        $waiter = Role::where('code', 'waiter')->first();
        if ($waiter && $waiter->permissions()->count() === 0) {
            $waiterPermissions = Permission::whereIn('code', [
                'access_pos', 'create_sales'
            ])->get();
            $waiter->permissions()->attach($waiterPermissions->pluck('id'));
        }

        // Chef
        $chef = Role::where('code', 'chef')->first();
        if ($chef && $chef->permissions()->count() === 0) {
            $chefPermissions = Permission::whereIn('code', [
                'view_kitchen', 'update_kitchen_status'
            ])->get();
            $chef->permissions()->attach($chefPermissions->pluck('id'));
        }

        // Accountant
        $accountant = Role::where('code', 'accountant')->first();
        if ($accountant && $accountant->permissions()->count() === 0) {
            $accountantPermissions = Permission::whereIn('code', [
                'view_reports', 'view_financial_reports', 'export_reports',
                'view_purchase_orders', 'view_vendors', 'view_audit_logs'
            ])->get();
            $accountant->permissions()->attach($accountantPermissions->pluck('id'));
        }

        // Viewer (read only)
        $viewer = Role::where('code', 'viewer')->first();
        if ($viewer && $viewer->permissions()->count() === 0) {
            $viewerPermissions = Permission::where('code', 'like', 'view_%')->get();
            $viewer->permissions()->attach($viewerPermissions->pluck('id'));
        }
    }
}