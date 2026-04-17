<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Department types lookup table
        Schema::create('department_types', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name');
            $table->string('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Insert default department types
        DB::table('department_types')->insert([
            ['code' => 'bar', 'name' => 'Bar', 'description' => 'Alcoholic and non-alcoholic beverages', 'sort_order' => 1, 'created_at' => now()],
            ['code' => 'cafe', 'name' => 'Cafe', 'description' => 'Coffee, tea, and light snacks', 'sort_order' => 2, 'created_at' => now()],
            ['code' => 'kitchen', 'name' => 'Kitchen', 'description' => 'Food preparation and cooking', 'sort_order' => 3, 'created_at' => now()],
            ['code' => 'pastry', 'name' => 'Pastry', 'description' => 'Bakery and dessert items', 'sort_order' => 4, 'created_at' => now()],
            ['code' => 'service', 'name' => 'Service', 'description' => 'Front of house and dining area', 'sort_order' => 5, 'created_at' => now()],
            ['code' => 'staff', 'name' => 'Staff Kitchen', 'description' => 'Staff meals and canteen', 'sort_order' => 6, 'created_at' => now()],
            ['code' => 'cleaning', 'name' => 'Cleaning', 'description' => 'Cleaning supplies and equipment', 'sort_order' => 7, 'created_at' => now()],
            ['code' => 'maintenance', 'name' => 'Maintenance', 'description' => 'Repair and maintenance items', 'sort_order' => 8, 'created_at' => now()],
            ['code' => 'office', 'name' => 'Office', 'description' => 'Administrative and office supplies', 'sort_order' => 9, 'created_at' => now()],
        ]);

        // Departments table
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name');
            $table->foreignId('department_type_id')->constrained('department_types')->restrictOnDelete();
            $table->foreignId('manager_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('default_store_id')->nullable()->constrained('stores')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            
            // Budget tracking (for finances)
            $table->decimal('monthly_budget', 15, 2)->nullable();
            $table->decimal('yearly_budget', 15, 2)->nullable();
            
            // Audit timestamps
            $table->timestamps();
            $table->softDeletes();
            
            // Audit trail
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
        });

        // Indexes
        Schema::table('departments', function (Blueprint $table) {
            $table->index('code');
            $table->index('department_type_id');
            $table->index('manager_id');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departments');
        Schema::dropIfExists('department_types');
    }
};