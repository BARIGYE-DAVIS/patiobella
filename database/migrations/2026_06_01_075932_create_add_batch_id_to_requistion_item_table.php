<?php
// database/migrations/2026_06_01_000000_add_batch_id_to_requisition_items.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('requisition_items', function (Blueprint $table) {
            $table->unsignedBigInteger('batch_id')->nullable()->after('requisition_id');
            $table->foreign('batch_id')->references('id')->on('batches')->onDelete('set null');

            // Add index for faster queries
            $table->index('batch_id');
        });
    }

    public function down()
    {
        Schema::table('requisition_items', function (Blueprint $table) {
            $table->dropForeign(['batch_id']);
            $table->dropColumn('batch_id');
        });
    }
};
