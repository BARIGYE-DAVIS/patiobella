<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('goods_received_note_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('goods_received_note_id')->constrained('goods_received_notes')->cascadeOnDelete();
            $table->foreignId('purchase_order_item_id')->constrained('purchase_order_items')->restrictOnDelete();
            $table->foreignId('inventory_item_id')->constrained('inventory_items')->restrictOnDelete();
            $table->foreignId('unit_id')->constrained('item_units')->restrictOnDelete();
            
            $table->decimal('quantity_received', 15, 6);
            $table->decimal('unit_cost', 15, 2);
            $table->decimal('total_cost', 15, 2)->default(0);
            
            $table->timestamps();
            
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->index('goods_received_note_id');
            $table->index('purchase_order_item_id');
            $table->index('inventory_item_id');
            $table->index('unit_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('goods_received_note_items');
    }
};