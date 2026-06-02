<?php
// database/migrations/2026_06_02_create_performance_items_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('performance_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('performance_summary_id')->constrained()->onDelete('cascade');
            $table->foreignId('menu_item_id')->constrained();
            $table->enum('ranking_type', ['top', 'bottom'])->default('top');
            $table->integer('rank_position')->default(0);
            $table->integer('quantity_sold')->default(0);
            $table->decimal('sales_amount', 15, 2)->default(0);
            $table->decimal('percentage_of_total_sales', 5, 2)->default(0);
            $table->decimal('cogs', 15, 2)->default(0);
            $table->decimal('profit', 15, 2)->default(0);
            $table->decimal('profit_margin', 5, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('performance_items');
    }
};
