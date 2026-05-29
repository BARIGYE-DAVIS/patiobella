<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('batches', function (Blueprint $table) {
            $table->id();
            $table->string('batch_number', 50)->unique();
            $table->foreignId('inventory_item_id')->constrained()->onDelete('cascade');
            $table->foreignId('goods_received_note_id')->constrained()->onDelete('cascade');
            $table->foreignId('supplier_id')->nullable()->constrained('vendors')->nullOnDelete();

            // Batch quantities
            $table->decimal('initial_quantity', 15, 6);
            $table->decimal('remaining_quantity', 15, 6);
            $table->decimal('unit_cost', 15, 2);
            $table->decimal('total_cost', 15, 2);

            // Tracking
            $table->string('base_unit', 50)->default('pcs');
            $table->date('manufacture_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('batch_status')->default('active'); // active, depleted, expired, partially_used

            // Optional
            $table->string('supplier_batch_number')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['inventory_item_id', 'batch_status']);
            $table->index('expiry_date');
            $table->index('remaining_quantity');
        });
    }

    public function down()
    {
        Schema::dropIfExists('batches');
    }
};
