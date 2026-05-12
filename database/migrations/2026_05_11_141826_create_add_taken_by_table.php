<?php
// database/migrations/2026_05_11_000001_add_taken_by_returned_by_to_stock_movements.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->string('taken_by')->nullable()->after('reason')
                ->comment('Person who took/received the items (STOCK OUT - issued to department)');
            $table->string('returned_by')->nullable()->after('taken_by')
                ->comment('Person who returned the items (STOCK IN - returned from department)');
        });
    }

    public function down()
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropColumn(['taken_by', 'returned_by']);
        });
    }
};
