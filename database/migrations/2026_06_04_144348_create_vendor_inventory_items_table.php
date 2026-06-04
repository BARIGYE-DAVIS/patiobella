<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('vendor_inventory_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('vendors')->onDelete('cascade');
            $table->foreignId('inventory_item_id')->constrained('inventory_items')->onDelete('cascade');

            // Additional fields for tracking
            $table->decimal('last_purchase_price', 15, 2)->nullable();
            $table->decimal('average_purchase_price', 15, 2)->nullable();
            $table->boolean('is_preferred')->default(false);
            $table->integer('lead_time_days')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            // Unique constraint to prevent duplicate vendor-item pairs
            $table->unique(['vendor_id', 'inventory_item_id']);

            // Indexes
            $table->index('vendor_id');
            $table->index('inventory_item_id');
            $table->index('is_preferred');
        });
    }

    public function down()
    {
        Schema::dropIfExists('vendor_inventory_items');
    }
};
