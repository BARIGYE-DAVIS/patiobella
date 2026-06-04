<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('performance_items', function (Blueprint $table) {
            // Add columns for ingredient tracking (needed for menu items with recipes)
            $table->foreignId('inventory_item_id')->nullable()->after('menu_item_id')->constrained('inventory_items')->onDelete('set null');
            $table->decimal('quantity_required', 12, 4)->default(0)->after('inventory_item_id');
            $table->decimal('used_quantity', 12, 2)->default(0)->after('closing_quantity');
            $table->decimal('opening_stock', 12, 2)->default(0)->after('used_quantity');
            $table->decimal('closing_stock', 12, 2)->default(0)->after('opening_stock');
        });
    }

    public function down()
    {
        Schema::table('performance_items', function (Blueprint $table) {
            $table->dropForeign(['inventory_item_id']);
            $table->dropColumn(['inventory_item_id', 'quantity_required', 'used_quantity', 'opening_stock', 'closing_stock']);
        });
    }
};
