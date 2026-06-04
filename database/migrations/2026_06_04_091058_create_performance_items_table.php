<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('performance_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('performance_report_id')->constrained('performance_reports')->onDelete('cascade');
            $table->foreignId('menu_item_id')->constrained('menu_items')->onDelete('cascade');

            // Stock quantities
            $table->decimal('opening_quantity', 12, 2)->default(0);
            $table->decimal('added_quantity', 12, 2)->default(0);
            $table->decimal('closing_quantity', 12, 2)->default(0);
            $table->decimal('quantity_sold', 12, 2)->default(0);

            // Cost and pricing
            $table->decimal('unit_cost', 15, 2)->default(0);
            $table->decimal('selling_price', 15, 2)->default(0);
            $table->decimal('cogs', 15, 2)->default(0);
            $table->decimal('sales_amount', 15, 2)->default(0);
            $table->decimal('profit', 15, 2)->default(0);
            $table->decimal('profit_margin', 8, 2)->default(0);

            $table->timestamps();

            // Indexes
            $table->index('performance_report_id');
            $table->index('menu_item_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('performance_items');
    }
};
