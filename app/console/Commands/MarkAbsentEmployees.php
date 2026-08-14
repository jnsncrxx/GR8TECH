<?php

namespace App\Console\Commands;

use App\Models\AttendanceRecord;
use App\Models\EmployeeSchedule;
use Illuminate\Console\Command;

class MarkAbsentEmployees extends Command
{
    // php artisan attendance:mark-absent --date=2026-07-14
    protected $signature = 'attendance:mark-absent {--date=}';

    protected $description = 'Marks employees as absent if they were scheduled to work but never timed in.';

    public function handle(): int
    {
        $date = $this->option('date')
            ? \Carbon\Carbon::parse($this->option('date'))
            : now();

        $dateStr = $date->format('Y-m-d');

        $workingSchedules = EmployeeSchedule::where('date', $dateStr)
            ->where('status', 'Working')
            ->get();

        $markedCount = 0;

        foreach ($workingSchedules as $schedule) {
            $existingRecord = AttendanceRecord::where('employee_id', $schedule->employee_id)
                ->where('date', $dateStr)
                ->first();

            // any existing record (time-in, leave, official business, etc.)
            // means something already handled this day - never overwrite it
            if ($existingRecord) {
                continue;
            }
            AttendanceRecord::updateOrCreate(
                [
                    'employee_id' => $schedule->employee_id,
                    'date' => $dateStr,
                ],
                [
                    'status' => AttendanceRecord::ABSENT,
                ]
            );

            $markedCount++;
        }

        $this->info("Marked {$markedCount} employee(s) as Absent for {$dateStr}.");

        return self::SUCCESS;
    }
}