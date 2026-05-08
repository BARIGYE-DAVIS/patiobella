<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('lpo_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lpo_id')->constrained('lpos')->onDelete('cascade');
            $table->foreignId('requisition_item_id')->constrained('requisition_items');
            $table->foreignId('inventory_item_id')->constrained('inventory_items');
            $table->decimal('quantity_approved', 12, 2);
            $table->decimal('unit_cost', 15, 2);
            $table->decimal('total_cost', 15, 2);
            $table->string('metrics')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('lpo_items');
    }
};