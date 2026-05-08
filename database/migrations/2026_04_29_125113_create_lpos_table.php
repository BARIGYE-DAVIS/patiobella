<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('lpos', function (Blueprint $table) {
            $table->id();
            $table->string('lpo_number')->unique();
            $table->foreignId('requisition_id')->constrained('requisitions');
            $table->foreignId('vendor_id')->constrained('vendors');
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->date('lpo_date');
            $table->date('expected_delivery_date')->nullable();
            $table->string('delivery_address')->nullable();
            $table->string('delivery_terms')->nullable();
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->enum('status', ['draft', 'pending_director', 'director_approved', 'director_rejected', 'issued'])->default('draft');
            $table->text('notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('lpos');
    }
};