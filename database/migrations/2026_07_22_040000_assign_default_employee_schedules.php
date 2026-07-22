<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Backfill the default company schedule for dates already covered by
     * existing payroll periods. Existing employee schedules are preserved.
     */
    public function up(): void
    {
        if (!Schema::hasTable('employees')
            || !Schema::hasTable('employee_schedules')
            || !Schema::hasTable('periods')) {
            return;
        }

        $periods = DB::table('periods')
            ->select('company_id', 'start_date', 'end_date')
            ->whereNotNull('start_date')
            ->whereNotNull('end_date')
            ->get();

        foreach ($periods as $period) {
            $employees = DB::table('employees')
                ->when($period->company_id, fn ($query) => $query->where('company_id', $period->company_id))
                ->select('id', 'department_id')
                ->get();

            $date = Carbon::parse($period->start_date)->startOfDay();
            $end = Carbon::parse($period->end_date)->startOfDay();

            while ($date->lte($end)) {
                $isSunday = $date->isSunday();

                foreach ($employees as $employee) {
                    if (!$employee->department_id) {
                        continue;
                    }

                    $exists = DB::table('employee_schedules')
                        ->where('employee_id', $employee->id)
                        ->whereDate('date', $date->toDateString())
                        ->exists();

                    if ($exists) {
                        continue;
                    }

                    DB::table('employee_schedules')->insert([
                        'id' => (string) Str::uuid(),
                        'employee_id' => $employee->id,
                        'department_id' => $employee->department_id,
                        'date' => $date->toDateString(),
                        'time_in' => $isSunday ? null : '08:00:00',
                        'time_out' => $isSunday ? null : '17:00:00',
                        'status' => $isSunday ? 'Day Off' : 'Working',
                        'schedule_type' => 'fixed',
                        'required_hours' => $isSunday ? 0 : 8,
                        'notes' => 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.',
                        'created_by' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                $date->addDay();
            }
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('employee_schedules')) {
            return;
        }

        DB::table('employee_schedules')
            ->where('notes', 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.')
            ->delete();
    }
};
