<?php
// database/migrations/2026_06_02_add_batch_issuances_and_returns_to_department_requisition_items.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('department_requisition_items', function (Blueprint $table) {
            $table->json('batch_issuances')->nullable()->after('batch_id');
            $table->json('batch_returns')->nullable()->after('batch_issuances');
        });
    }

    public function down()
    {
        Schema::table('department_requisition_items', function (Blueprint $table) {
            $table->dropColumn('batch_issuances');
            $table->dropColumn('batch_returns');
        });
    }
};
