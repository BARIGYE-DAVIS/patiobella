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
        // Store types lookup table
        Schema::create('store_types', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name');
            $table->string('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Stores table
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name');
            $table->string('location')->nullable();
            $table->foreignId('store_type_id')->constrained('store_types')->restrictOnDelete();
            $table->foreignId('manager_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            
            // Audit timestamps
            $table->timestamps();
            $table->softDeletes();
            
            // Audit trail
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
        });

        // Indexes
        Schema::table('store_types', function (Blueprint $table) {
            $table->index('code');
            $table->index('is_active');
        });

        Schema::table('stores', function (Blueprint $table) {
            $table->index('code');
            $table->index('store_type_id');
            $table->index('manager_id');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stores');
        Schema::dropIfExists('store_types');
    }
};