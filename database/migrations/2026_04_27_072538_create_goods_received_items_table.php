<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('goods_received_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('goods_received_note_id')->constrained('goods_received_notes')->onDelete('cascade');
            $table->foreignId('purchase_order_item_id')->constrained('purchase_order_items');
            $table->foreignId('inventory_item_id')->constrained('inventory_items');
            $table->decimal('quantity_ordered', 12, 2);
            $table->decimal('quantity_received', 12, 2);
            $table->decimal('quantity_accepted', 12, 2);
            $table->decimal('quantity_rejected', 12, 2)->default(0);
            $table->string('rejection_reason')->nullable();
            $table->decimal('unit_cost', 12, 2);
            $table->decimal('total_cost', 12, 2);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('goods_received_items');
    }
};