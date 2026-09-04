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
        Schema::table('payroll_adjustments', function (Blueprint $table) {
            if (!Schema::hasColumn('payroll_adjustments', 'company_id')) {
                $table->char('company_id', 36)->collation('utf8mb4_unicode_ci')->nullable()->after('id')->index();
            }
            if (!Schema::hasColumn('payroll_adjustments', 'employee_id')) {
                $table->char('employee_id', 36)->collation('utf8mb4_unicode_ci')->nullable()->after('company_id')->index();
            }
            if (!Schema::hasColumn('payroll_adjustments', 'category')) {
                $table->enum('category', ['bonus', 'allowance', 'deduction', 'manual'])->default('manual')->after('name');
            }
            if (!Schema::hasColumn('payroll_adjustments', 'direction')) {
                $table->enum('direction', ['earning', 'deduction'])->default('earning')->after('category');
            }
            if (!Schema::hasColumn('payroll_adjustments', 'frequency')) {
                $table->enum('frequency', ['one_time', 'recurring'])->default('one_time')->after('direction');
            }
            if (!Schema::hasColumn('payroll_adjustments', 'effective_from')) {
                $table->date('effective_from')->nullable()->after('amount');
            }
            if (!Schema::hasColumn('payroll_adjustments', 'effective_to')) {
                $table->date('effective_to')->nullable()->after('effective_from');
            }
            if (!Schema::hasColumn('payroll_adjustments', 'is_taxable')) {
                $table->boolean('is_taxable')->default(false)->after('effective_to');
            }
            if (!Schema::hasColumn('payroll_adjustments', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('is_taxable');
            }
            if (!Schema::hasColumn('payroll_adjustments', 'reason')) {
                $table->text('reason')->nullable()->after('is_active');
            }
            if (!Schema::hasColumn('payroll_adjustments', 'created_by')) {
                $table->char('created_by', 36)->collation('utf8mb4_unicode_ci')->nullable()->after('reason');
            }
            if (!Schema::hasColumn('payroll_adjustments', 'updated_by')) {
                $table->char('updated_by', 36)->collation('utf8mb4_unicode_ci')->nullable()->after('created_by');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payroll_adjustments', function (Blueprint $table) {
            $columnsToDrop = [];
            foreach (['company_id', 'employee_id', 'category', 'direction', 'frequency', 'effective_from', 'effective_to', 'is_taxable', 'is_active', 'reason', 'created_by', 'updated_by'] as $column) {
                if (Schema::hasColumn('payroll_adjustments', $column)) {
                    $columnsToDrop[] = $column;
                }
            }
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
