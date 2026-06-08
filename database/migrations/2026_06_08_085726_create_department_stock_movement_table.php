<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('department_stock_movements', function (Blueprint $table) {
            $table->id();
            $table->string('movement_number', 50)->unique();
            $table->unsignedBigInteger('department_id');
            $table->unsignedBigInteger('inventory_item_id');
            $table->unsignedBigInteger('batch_id')->nullable();
            $table->unsignedBigInteger('requisition_item_id')->nullable();

            $table->decimal('opening_balance', 15, 6)->default(0);
            $table->decimal('added_quantity', 15, 6)->default(0);
            $table->decimal('used_quantity', 15, 6)->default(0);
            $table->decimal('returned_quantity', 15, 6)->default(0);
            $table->decimal('closing_balance', 15, 6)->default(0);

            $table->enum('movement_type', ['issue', 'return', 'consumption', 'sale', 'adjustment']);
            $table->date('movement_date');
            $table->text('notes')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Fixed index names (shorter)
            $table->index(['department_id', 'inventory_item_id', 'movement_date'], 'dept_stock_dept_item_date_idx');
            $table->index('movement_number', 'dept_stock_mvmt_no_idx');
            $table->index('movement_date', 'dept_stock_date_idx');
            $table->index('department_id', 'dept_stock_dept_idx');
            $table->index('inventory_item_id', 'dept_stock_item_idx');
            $table->index('batch_id', 'dept_stock_batch_idx');
            $table->index('requisition_item_id', 'dept_stock_req_item_idx');

            // Foreign keys
            $table->foreign('department_id')->references('id')->on('departments');
            $table->foreign('inventory_item_id')->references('id')->on('inventory_items');
            $table->foreign('batch_id')->references('id')->on('batches');
            $table->foreign('requisition_item_id')->references('id')->on('department_requisition_items');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('department_stock_movements');
    }
};
