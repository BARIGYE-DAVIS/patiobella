<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('po_number', 50)->unique();
            $table->foreignId('vendor_id')->constrained('vendors')->restrictOnDelete();
            $table->foreignId('store_id')->constrained('stores')->restrictOnDelete();
            $table->foreignId('ordered_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->date('po_date');
            $table->date('expected_delivery_date')->nullable();
            
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            
            $table->enum('status', [
                'draft', 'approved', 'sent', 
                'partially_received', 'fully_received', 'cancelled'
            ])->default('draft');
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->index('po_number');
            $table->index('vendor_id');
            $table->index('store_id');
            $table->index('status');
            $table->index('po_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};