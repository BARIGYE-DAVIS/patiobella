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
        Schema::create('units_of_measure', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name');
            $table->string('symbol', 20)->nullable();
            $table->string('description')->nullable();
            $table->boolean('is_base_unit')->default(false);
            $table->foreignId('base_unit_id')->nullable()->constrained('units_of_measure')->nullOnDelete();
            $table->decimal('conversion_factor', 15, 6)->nullable()->comment('Factor to convert to base unit');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Indexes
        Schema::table('units_of_measure', function (Blueprint $table) {
            $table->index('code');
            $table->index('is_active');
            $table->index('is_base_unit');
            $table->index('base_unit_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units_of_measure');
    }
};