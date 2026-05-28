<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_order_id')->nullable();
            $table->unsignedBigInteger('grn_id')->nullable();
            $table->string('document_type')->nullable();
            $table->string('filename');
            $table->string('original_name');
            $table->string('path');
            $table->integer('file_size')->nullable();
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('uploaded_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('purchase_order_id')->references('id')->on('purchase_orders')->onDelete('cascade');
            $table->foreign('grn_id')->references('id')->on('goods_received_notes')->onDelete('cascade');
            $table->foreign('uploaded_by')->references('id')->on('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('documents');
    }
};
