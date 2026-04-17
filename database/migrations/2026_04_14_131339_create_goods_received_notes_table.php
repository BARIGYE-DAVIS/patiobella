<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('goods_received_notes', function (Blueprint $table) {
            $table->id();
            $table->string('grn_number', 50)->unique();
            $table->foreignId('purchase_order_id')->constrained('purchase_orders')->restrictOnDelete();
            $table->foreignId('vendor_id')->constrained('vendors')->restrictOnDelete();
            $table->foreignId('store_id')->constrained('stores')->restrictOnDelete();
            $table->foreignId('received_by')->constrained('users')->restrictOnDelete();
            $table->date('received_date');
            $table->string('delivery_note_number', 100)->nullable();
            
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            
            $table->enum('status', ['draft', 'completed', 'cancelled'])->default('draft');
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->index('grn_number');
            $table->index('purchase_order_id');
            $table->index('vendor_id');
            $table->index('store_id');
            $table->index('status');
            $table->index('received_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('goods_received_notes');
    }
};