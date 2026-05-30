<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('signature_path')->nullable()->after('remember_token');
            $table->timestamp('signature_updated_at')->nullable()->after('signature_path');
            $table->unsignedBigInteger('signature_updated_by')->nullable()->after('signature_updated_at');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['signature_path', 'signature_updated_at', 'signature_updated_by']);
        });
    }
};
