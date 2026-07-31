<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('overtime_reminders')) {
            Schema::create('overtime_reminders', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('employee_id');
                $table->uuid('attendance_record_id')->nullable();
                $table->date('date');
                $table->decimal('required_hours', 8, 2)->default(8.00);
                $table->decimal('worked_hours', 8, 2)->default(0.00);
                $table->decimal('extra_hours', 8, 2)->default(0.00);
                $table->dateTime('start_time')->nullable();
                $table->dateTime('end_time')->nullable();
                $table->enum('status', ['pending', 'submitted', 'dismissed'])->default('pending');
                $table->timestamps();

                $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
                $table->foreign('attendance_record_id')->references('id')->on('attendance_records')->onDelete('cascade');
                
                $table->unique(['employee_id', 'date'], 'emp_date_unique_ot_reminder');
                $table->index(['employee_id', 'status']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('overtime_reminders');
    }
};
