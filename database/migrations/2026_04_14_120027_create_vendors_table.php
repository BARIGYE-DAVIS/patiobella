<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->string('vendor_code')->unique();
            $table->string('name');
            $table->string('contact_person')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('alternative_phone')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->default('Uganda');
            $table->string('tax_id')->nullable()->comment('TIN / VAT registration number');
            $table->enum('payment_method', ['cash', 'bank', 'mobile'])->default('cash');
            $table->integer('credit_limit')->nullable()->comment('Maximum credit allowed');
            $table->enum('status', ['active', 'inactive', 'blacklisted'])->default('active');
            $table->text('notes')->nullable();
            
            // Audit timestamps
            $table->timestamps();
            $table->softDeletes();
            
            // Audit trail - who created/updated this vendor
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
        });

        // Add indexes for faster lookups
        Schema::table('vendors', function (Blueprint $table) {
            $table->index('vendor_code');
            $table->index('name');
            $table->index('status');
            $table->index('email');
            $table->index('phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};