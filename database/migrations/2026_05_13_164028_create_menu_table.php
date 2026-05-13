<?php
// database/migrations/2026_05_13_000001_create_menu_items_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('category'); // Appetizer, Main, Dessert, Beverage
            $table->decimal('selling_price', 12, 2);
            $table->integer('preparation_time')->nullable()->comment('Minutes');
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('inventory_item_id')->nullable()->comment('Optional link to inventory');
            $table->text('allergen_info')->nullable();
            $table->string('image_url')->nullable();
            $table->integer('sort_order')->default(0);
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('inventory_item_id')->references('id')->on('inventory_items')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');

            $table->index('category');
            $table->index('is_active');
        });
    }

    public function down()
    {
        Schema::dropIfExists('menu_items');
    }
};
