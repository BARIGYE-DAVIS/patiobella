<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // =====================================================
        // 1. STOCK MOVEMENT TYPES (Lookup table)
        // =====================================================
        Schema::create('stock_movement_types', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name');
            $table->enum('sign', ['+', '-']);
            $table->string('description')->nullable();
            $table->boolean('requires_approval')->default(false);
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('code');
            $table->index('sign');
        });

        // =====================================================
        // 2. STOCK MOVEMENTS (Main transaction table)
        // =====================================================
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->string('movement_number', 100)->unique();
            $table->foreignId('inventory_item_id')->constrained('inventory_items')->restrictOnDelete();
            $table->foreignId('store_id')->constrained('stores')->restrictOnDelete();
            $table->foreignId('movement_type_id')->constrained('stock_movement_types')->restrictOnDelete();
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            
            // Movement quantity with unit support
            $table->decimal('quantity', 15, 6);
            $table->foreignId('unit_id')->constrained('item_units')->restrictOnDelete();
            $table->decimal('quantity_in_base_unit', 15, 6)->default(0)->comment('Calculate in model: quantity × unit.quantity_in_base_unit');
            
            // Cost at movement time
            $table->decimal('unit_cost', 15, 2)->nullable();
            $table->decimal('total_value', 15, 2)->nullable();
            
            $table->text('reason')->nullable();
            $table->date('movement_date');
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            
            // Source documents - NO FOREIGN KEY CONSTRAINTS
            $table->unsignedBigInteger('purchase_order_id')->nullable();
            $table->unsignedBigInteger('goods_received_note_id')->nullable();
            // $table->foreignId('stock_take_id')->nullable()->constrained('stock_takes')->nullOnDelete(); // Commented - table not created yet
            
            // Reversal tracking
            $table->boolean('is_reversed')->default(false);
            $table->foreignId('reversed_by_movement_id')->nullable()->constrained('stock_movements')->nullOnDelete();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            
            // Indexes (no foreign key constraints)
            $table->index('movement_number');
            $table->index('inventory_item_id');
            $table->index('store_id');
            $table->index('movement_type_id');
            $table->index('department_id');
            $table->index('movement_date');
            $table->index('unit_id');
            $table->index('purchase_order_id');
            $table->index('goods_received_note_id');
            $table->index('is_reversed');
        });

        // =====================================================
        // 3. STOCK BALANCES (Summary table - for quick lookups)
        // =====================================================
        Schema::create('stock_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_item_id')->constrained('inventory_items')->restrictOnDelete();
            $table->foreignId('store_id')->constrained('stores')->restrictOnDelete();
            $table->decimal('quantity', 15, 6)->default(0)->comment('Always in base unit');
            $table->decimal('average_cost', 15, 2)->default(0);
            $table->date('balance_date');
            $table->timestamps();
            
            $table->unique(['inventory_item_id', 'store_id', 'balance_date'], 'stock_balances_unique');
            
            $table->index('inventory_item_id');
            $table->index('store_id');
            $table->index('balance_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_balances');
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('stock_movement_types');
    }
};