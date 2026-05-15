<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->decimal('amount_paid', 12, 2)->nullable()->after('total_amount');
            $table->decimal('change_amount', 12, 2)->default(0)->after('amount_paid');
        });
    }

    public function down()
    {
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->dropColumn('amount_paid');
            $table->dropColumn('change_amount');
        });
    }
};
