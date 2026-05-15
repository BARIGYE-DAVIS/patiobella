<?php
// database/migrations/2026_05_15_000001_add_inventory_item_id_and_item_name_to_sales_order_items.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sales_order_items', function (Blueprint $table) {
            // Add inventory_item_id column (nullable because menu items won't have this)
            $table->unsignedBigInteger('inventory_item_id')->nullable()->after('menu_item_id');

            // Add item_name column to store the name at time of sale (for both menu and inventory items)
            $table->string('item_name')->nullable()->after('inventory_item_id');

            // Add foreign key constraint for inventory_item_id
            $table->foreign('inventory_item_id')
                  ->references('id')
                  ->on('inventory_items')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_order_items', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['inventory_item_id']);

            // Drop the columns
            $table->dropColumn('inventory_item_id');
            $table->dropColumn('item_name');
        });
    }
};
