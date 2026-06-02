<?php
// database/migrations/2026_06_02_create_daily_sales_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('daily_sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_item_id')->constrained();
            $table->foreignId('department_id')->constrained();
            $table->date('sale_date');
            $table->integer('quantity_sold')->default(0);
            $table->decimal('unit_price', 15, 2)->default(0); // Selling price at time of sale
            $table->decimal('total_amount', 15, 2)->default(0); // quantity × unit_price
            $table->decimal('calculated_cogs', 15, 2)->default(0); // Auto-calculated from recipe
            $table->decimal('calculated_profit', 15, 2)->default(0); // total_amount - calculated_cogs
            $table->foreignId('created_by')->constrained('users');
            $table->text('notes')->nullable();
            $table->timestamps();

            // Unique constraint to prevent duplicate entry for same item on same day
            $table->unique(['menu_item_id', 'department_id', 'sale_date'], 'unique_daily_sale');
        });
    }

    public function down()
    {
        Schema::dropIfExists('daily_sales');
    }
};
