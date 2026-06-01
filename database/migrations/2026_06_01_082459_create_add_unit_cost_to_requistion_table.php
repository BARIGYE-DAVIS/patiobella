<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('requisition_items', function (Blueprint $table) {
            $table->decimal('unit_cost', 15, 2)->nullable()->after('quantity_requested');
        });
    }

    public function down()
    {
        Schema::table('requisition_items', function (Blueprint $table) {
            $table->dropColumn('unit_cost');
        });
    }
};
