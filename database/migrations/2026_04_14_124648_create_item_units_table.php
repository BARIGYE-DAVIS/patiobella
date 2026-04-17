<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_item_id')->constrained('inventory_items')->cascadeOnDelete();
            $table->foreignId('unit_of_measure_id')->constrained('units_of_measure')->restrictOnDelete();
            
            // Unit hierarchy
            $table->boolean('is_base_unit')->default(false)->comment('Smallest unit, e.g., piece, bottle, kg');
            $table->decimal('quantity_in_base_unit', 15, 6)->default(1)->comment('e.g., 1 crate = 24 pieces, so quantity = 24');
            
            // Pricing for this unit
            $table->decimal('last_purchase_price', 15, 2)->nullable()->comment('Last price paid for this unit');
            $table->decimal('average_purchase_price', 15, 2)->nullable()->comment('Weighted average cost for this unit');
            $table->decimal('selling_price', 15, 2)->nullable()->comment('Menu/sales price for this unit');
            
            // Barcode at unit level (e.g., crate barcode vs piece barcode)
            $table->string('barcode', 100)->nullable()->unique();
            
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Each item can have multiple units, but only one base unit
            $table->unique(['inventory_item_id', 'unit_of_measure_id'], 'item_unit_unique');
        });

        Schema::table('item_units', function (Blueprint $table) {
            $table->index('inventory_item_id');
            $table->index('unit_of_measure_id');
            $table->index('is_base_unit');
            $table->index('barcode');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_units');
    }
};