<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The old "sick_leave_days" / "sick_leave_pay" columns actually store the
 * total of ALL approved paid-leave types (vacation, sick, etc.), not just
 * sick leave. Renaming to "paid_leave_days" / "paid_leave_pay" so the
 * column names match what they actually compute and don't mislead anyone
 * reading the DB or the payroll code.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            if (Schema::hasColumn('payrolls', 'sick_leave_days') && !Schema::hasColumn('payrolls', 'paid_leave_days')) {
                $table->renameColumn('sick_leave_days', 'paid_leave_days');
            }
            if (Schema::hasColumn('payrolls', 'sick_leave_pay') && !Schema::hasColumn('payrolls', 'paid_leave_pay')) {
                $table->renameColumn('sick_leave_pay', 'paid_leave_pay');
            }
        });
    }

    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            if (Schema::hasColumn('payrolls', 'paid_leave_days') && !Schema::hasColumn('payrolls', 'sick_leave_days')) {
                $table->renameColumn('paid_leave_days', 'sick_leave_days');
            }
            if (Schema::hasColumn('payrolls', 'paid_leave_pay') && !Schema::hasColumn('payrolls', 'sick_leave_pay')) {
                $table->renameColumn('paid_leave_pay', 'sick_leave_pay');
            }
        });
    }
};
