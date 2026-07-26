<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Uses hasColumn() guards so it is safe to re-run if some columns already exist.
     */
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            if (!Schema::hasColumn('employees', 'profile_photo')) {
                $table->string('profile_photo')->nullable()->after('employee_id');
            }
            if (!Schema::hasColumn('employees', 'middle_name')) {
                $table->string('middle_name')->nullable()->after('last_name');
            }
            if (!Schema::hasColumn('employees', 'sex')) {
                $table->string('sex')->nullable()->after('middle_name');
            }
            if (!Schema::hasColumn('employees', 'employee_status')) {
                $table->string('employee_status')->nullable()->after('hire_date');
            }
            if (!Schema::hasColumn('employees', 'contract_end_date')) {
                $table->date('contract_end_date')->nullable()->after('employee_status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $columns = [];
        foreach (['profile_photo', 'middle_name', 'sex', 'employee_status', 'contract_end_date'] as $col) {
            if (Schema::hasColumn('employees', $col)) {
                $columns[] = $col;
            }
        }
        if (!empty($columns)) {
            Schema::table('employees', function (Blueprint $table) use ($columns) {
                $table->dropColumn($columns);
            });
        }
    }
};
