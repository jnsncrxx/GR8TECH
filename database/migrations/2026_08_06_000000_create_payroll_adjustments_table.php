<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll_adjustments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('company_id')->nullable()->index();
            $table->uuid('employee_id')->index();
            $table->string('name');
            $table->enum('category', ['bonus', 'allowance', 'deduction', 'manual']);
            $table->enum('direction', ['earning', 'deduction']);
            $table->enum('frequency', ['one_time', 'recurring'])->default('one_time');
            $table->decimal('amount', 12, 2);
            $table->date('effective_from');
            $table->date('effective_to')->nullable();
            $table->boolean('is_taxable')->default(false);
            $table->boolean('is_active')->default(true);
            $table->text('reason');
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('companies')->nullOnDelete();
            $table->foreign('employee_id')->references('id')->on('employees')->cascadeOnDelete();
            $table->foreign('created_by')->references('id')->on('accounts')->nullOnDelete();
            $table->foreign('updated_by')->references('id')->on('accounts')->nullOnDelete();
            $table->index(['employee_id', 'effective_from', 'effective_to'], 'payroll_adjustments_employee_dates_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_adjustments');
    }
};
