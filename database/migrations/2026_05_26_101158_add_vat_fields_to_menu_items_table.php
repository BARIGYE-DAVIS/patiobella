<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('menu_items', function (Blueprint $table) {
            // Add VAT fields
            $table->decimal('vat_rate', 5, 2)->default(18.00)->after('final_margin');
            $table->decimal('vat_amount', 12, 2)->default(0.00)->after('vat_rate');
            $table->boolean('vat_inclusive')->default(true)->after('vat_amount');
            $table->decimal('net_price', 12, 2)->nullable()->after('vat_inclusive');
        });
    }

    public function down()
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropColumn(['vat_rate', 'vat_amount', 'vat_inclusive', 'net_price']);
        });
    }
};
