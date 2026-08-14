<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('payrolls', 'period_id')) {
            Schema::table('payrolls', function (Blueprint $table) {
                $table->uuid('period_id')->nullable()->after('employee_id');
                $table->index('period_id', 'payrolls_period_id_index');
            });
        }

        // Link legacy payroll rows to an exact period using cutoff dates.
        // When duplicate date ranges exist, employee company is used to select
        // the correct company period whenever possible.
        DB::table('payrolls')
            ->whereNull('period_id')
            ->orderBy('id')
            ->chunkById(200, function ($payrolls) {
                foreach ($payrolls as $payroll) {
                    $query = DB::table('periods')
                        ->whereDate('start_date', $payroll->pay_period_start)
                        ->whereDate('end_date', $payroll->pay_period_end);

                    $employeeCompanyId = DB::table('employees')
                        ->where('id', $payroll->employee_id)
                        ->value('company_id');

                    if ($employeeCompanyId && Schema::hasColumn('periods', 'company_id')) {
                        $query->where('company_id', $employeeCompanyId);
                    }

                    $periodId = $query->orderByDesc('created_at')->value('id');

                    if ($periodId) {
                        DB::table('payrolls')
                            ->where('id', $payroll->id)
                            ->update(['period_id' => $periodId]);
                    }
                }
            }, 'id');

        Schema::table('payrolls', function (Blueprint $table) {
            $table->foreign('period_id', 'payrolls_period_id_foreign')
                ->references('id')
                ->on('periods')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('payrolls', 'period_id')) {
            Schema::table('payrolls', function (Blueprint $table) {
                $table->dropForeign('payrolls_period_id_foreign');
                $table->dropIndex('payrolls_period_id_index');
                $table->dropColumn('period_id');
            });
        }
    }
};
