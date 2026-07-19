<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Enforces "one payroll per employee per pay period" at the database level.
 *
 * Payroll::updateOrCreate() in PayrollGenerationService already does a
 * SELECT-then-INSERT/UPDATE keyed on (employee_id, pay_period_start,
 * pay_period_end), which is correct for sequential requests but is not
 * atomic: two concurrent "Generate Payroll" submissions for the same
 * employee/period can both miss the SELECT and both INSERT, producing a
 * real duplicate row. This unique index makes that impossible at the DB
 * layer; the second concurrent insert will fail with a unique-constraint
 * violation instead of silently creating a duplicate. See
 * calculatePayrollFromRecords() in PayrollGenerationService.php for the
 * matching application-level handling of that violation.
 *
 * Before/if this runs on a database that may already contain legitimate
 * duplicates (same employee + exact same period, e.g. from testing with a
 * unique constraint not yet in place), the up() method de-duplicates first
 * by keeping only the most recently created row per (employee_id,
 * pay_period_start, pay_period_end) group and deleting the rest. Review
 * that data before running in production if payroll history matters.
 */
return new class extends Migration
{
    public function up(): void
    {
        // De-duplicate existing rows first so the unique index can be added
        // cleanly. Keeps the most recently created row per
        // (employee_id, pay_period_start, pay_period_end) group.
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement(<<<SQL
                DELETE p1 FROM payrolls p1
                INNER JOIN payrolls p2
                    ON p1.employee_id = p2.employee_id
                    AND p1.pay_period_start = p2.pay_period_start
                    AND p1.pay_period_end = p2.pay_period_end
                    AND p1.created_at < p2.created_at
            SQL);
        } else {
            // Portable fallback (SQLite/Postgres/etc.)
            $duplicateIds = DB::table('payrolls as p1')
                ->join('payrolls as p2', function ($join) {
                    $join->on('p1.employee_id', '=', 'p2.employee_id')
                        ->on('p1.pay_period_start', '=', 'p2.pay_period_start')
                        ->on('p1.pay_period_end', '=', 'p2.pay_period_end')
                        ->where('p1.created_at', '<', DB::raw('p2.created_at'));
                })
                ->pluck('p1.id');

            if ($duplicateIds->isNotEmpty()) {
                DB::table('payrolls')->whereIn('id', $duplicateIds)->delete();
            }
        }

        Schema::table('payrolls', function (Blueprint $table) {
            $table->unique(
                ['employee_id', 'pay_period_start', 'pay_period_end'],
                'payrolls_employee_period_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropUnique('payrolls_employee_period_unique');
        });
    }
};
