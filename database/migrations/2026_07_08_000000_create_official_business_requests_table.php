<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('official_business_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // NOTE: assumes employees.id is a UUID, matching attendance_records.employee_id.
            // If your employees table uses a bigint PK instead, change this to
            // $table->unsignedBigInteger('employee_id') and adjust the FK below.
            $table->uuid('employee_id');

            $table->date('date');
            $table->text('reason');
            $table->string('status')->default('pending'); // pending | approved | rejected

            // NOTE: assumes users.id is the default Laravel bigint PK.
            // If reviewers/creators are tracked via a UUID users table, change these
            // to $table->uuid(...) instead.
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();

            $table->timestamp('reviewed_at')->nullable();
            $table->text('rejection_reason')->nullable();

            // Set once the request is approved and the AttendanceRecord is created
            $table->uuid('attendance_record_id')->nullable();

            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('attendance_record_id')->references('id')->on('attendance_records')->onDelete('set null');

            $table->index(['employee_id', 'date']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('official_business_requests');
    }
};
