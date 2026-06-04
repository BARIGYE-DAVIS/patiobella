<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('performance_reports', function (Blueprint $table) {
            $table->id();
            $table->string('report_number', 50)->unique();
            $table->foreignId('department_id')->constrained('departments')->onDelete('cascade');
            $table->date('report_date');
            $table->decimal('total_sales', 15, 2)->default(0);
            $table->decimal('total_cogs', 15, 2)->default(0);
            $table->decimal('total_profit', 15, 2)->default(0);
            $table->decimal('profit_margin', 8, 2)->default(0);
            $table->enum('status', ['draft', 'completed', 'cancelled'])->default('completed');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('report_date');
            $table->index('department_id');
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('performance_reports');
    }
};
