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
        // Main categories lookup table
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name');
            $table->string('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Insert default categories (based on the Excel data)
        DB::table('categories')->insert([
            ['code' => 'BEV-SODA', 'name' => 'Soft Drinks', 'description' => 'Coke, Fanta, Sprite, Pepsi, etc.', 'sort_order' => 1, 'created_at' => now()],
            ['code' => 'BEV-WATER', 'name' => 'Water', 'description' => 'Still and sparkling water', 'sort_order' => 2, 'created_at' => now()],
            ['code' => 'BEV-ENERGY', 'name' => 'Energy Drinks', 'description' => 'Red Bull, etc.', 'sort_order' => 3, 'created_at' => now()],
            ['code' => 'BEER', 'name' => 'Beer', 'description' => 'All beer brands', 'sort_order' => 4, 'created_at' => now()],
            ['code' => 'SPIRITS', 'name' => 'Spirits', 'description' => 'Vodka, Whisky, Gin, Rum, Tequila', 'sort_order' => 5, 'created_at' => now()],
            ['code' => 'WINE', 'name' => 'Wine', 'description' => 'Red, white, rose, sparkling', 'sort_order' => 6, 'created_at' => now()],
            ['code' => 'MIXERS', 'name' => 'Mixers & Syrups', 'description' => 'Tonic, soda water, syrups, purees', 'sort_order' => 7, 'created_at' => now()],
            ['code' => 'DAIRY', 'name' => 'Dairy', 'description' => 'Milk, cream, yoghurt, cheese, butter', 'sort_order' => 8, 'created_at' => now()],
            ['code' => 'MEAT', 'name' => 'Meat & Poultry', 'description' => 'Chicken, beef, pork, goat, fish', 'sort_order' => 9, 'created_at' => now()],
            ['code' => 'PRODUCE', 'name' => 'Fresh Produce', 'description' => 'Vegetables, fruits, herbs', 'sort_order' => 10, 'created_at' => now()],
            ['code' => 'DRY-GOODS', 'name' => 'Dry Goods', 'description' => 'Rice, flour, pasta, beans, sugar', 'sort_order' => 11, 'created_at' => now()],
            ['code' => 'OIL-FAT', 'name' => 'Oils & Fats', 'description' => 'Cooking oil, butter, ghee', 'sort_order' => 12, 'created_at' => now()],
            ['code' => 'SAUCES', 'name' => 'Sauces & Condiments', 'description' => 'Ketchup, mayo, soy sauce, vinegar', 'sort_order' => 13, 'created_at' => now()],
            ['code' => 'SPICES', 'name' => 'Spices & Seasonings', 'description' => 'Salt, pepper, paprika, curry, herbs', 'sort_order' => 14, 'created_at' => now()],
            ['code' => 'BAKERY', 'name' => 'Bakery', 'description' => 'Bread, buns, cakes, pastries', 'sort_order' => 15, 'created_at' => now()],
            ['code' => 'ICE-CREAM', 'name' => 'Ice Cream', 'description' => 'All ice cream flavors', 'sort_order' => 16, 'created_at' => now()],
            ['code' => 'CLEANING', 'name' => 'Cleaning Supplies', 'description' => 'Soap, disinfectant, garbage bags, gloves', 'sort_order' => 17, 'created_at' => now()],
            ['code' => 'PACKAGING', 'name' => 'Packaging', 'description' => 'Takeaway boxes, cups, lids, bags, foil', 'sort_order' => 18, 'created_at' => now()],
            ['code' => 'OFFICE', 'name' => 'Office Supplies', 'description' => 'Paper, pens, printer supplies', 'sort_order' => 19, 'created_at' => now()],
            ['code' => 'EQUIPMENT', 'name' => 'Equipment & Utensils', 'description' => 'Glasses, plates, kitchen tools', 'sort_order' => 20, 'created_at' => now()],
        ]);

        // Sub-categories table (for more granular classification)
        Schema::create('sub_categories', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name');
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->string('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Insert sample sub-categories
        DB::table('sub_categories')->insert([
            // Soft Drinks sub-categories
            ['code' => 'COLA', 'name' => 'Cola', 'category_id' => 1, 'sort_order' => 1, 'created_at' => now()],
            ['code' => 'ORANGE', 'name' => 'Orange', 'category_id' => 1, 'sort_order' => 2, 'created_at' => now()],
            ['code' => 'LEMON-LIME', 'name' => 'Lemon-Lime', 'category_id' => 1, 'sort_order' => 3, 'created_at' => now()],
            ['code' => 'GINGER', 'name' => 'Ginger Beer/Ale', 'category_id' => 1, 'sort_order' => 4, 'created_at' => now()],
            
            // Spirits sub-categories
            ['code' => 'VODKA', 'name' => 'Vodka', 'category_id' => 5, 'sort_order' => 1, 'created_at' => now()],
            ['code' => 'WHISKY', 'name' => 'Whisky', 'category_id' => 5, 'sort_order' => 2, 'created_at' => now()],
            ['code' => 'GIN', 'name' => 'Gin', 'category_id' => 5, 'sort_order' => 3, 'created_at' => now()],
            ['code' => 'RUM', 'name' => 'Rum', 'category_id' => 5, 'sort_order' => 4, 'created_at' => now()],
            ['code' => 'TEQUILA', 'name' => 'Tequila', 'category_id' => 5, 'sort_order' => 5, 'created_at' => now()],
            ['code' => 'LIQUEUR', 'name' => 'Liqueur', 'category_id' => 5, 'sort_order' => 6, 'created_at' => now()],
            
            // Meat sub-categories
            ['code' => 'CHICKEN', 'name' => 'Chicken', 'category_id' => 9, 'sort_order' => 1, 'created_at' => now()],
            ['code' => 'BEEF', 'name' => 'Beef', 'category_id' => 9, 'sort_order' => 2, 'created_at' => now()],
            ['code' => 'PORK', 'name' => 'Pork', 'category_id' => 9, 'sort_order' => 3, 'created_at' => now()],
            ['code' => 'GOAT', 'name' => 'Goat', 'category_id' => 9, 'sort_order' => 4, 'created_at' => now()],
            ['code' => 'FISH', 'name' => 'Fish', 'category_id' => 9, 'sort_order' => 5, 'created_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_categories');
        Schema::dropIfExists('categories');
    }
};