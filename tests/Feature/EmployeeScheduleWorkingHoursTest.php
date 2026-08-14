<?php

namespace Tests\Feature;

use App\Models\EmployeeSchedule;
use Tests\TestCase;

/**
 * Guards EmployeeSchedule::getWorkingHoursAttribute against the same
 * "Y-m-d Y-m-d H:i:s" double-date failure: the time value is normalized to
 * time-only before the datetime is constructed, so a value that already
 * carries a date can no longer break the calculation.
 *
 * These cases exercise only cast attributes, so no database row is needed.
 */
class EmployeeScheduleWorkingHoursTest extends TestCase
{
    private function schedule(string $timeIn, string $timeOut): EmployeeSchedule
    {
        return new EmployeeSchedule([
            'date' => '2026-07-14',
            'time_in' => $timeIn,
            'time_out' => $timeOut,
            'status' => 'Working',
        ]);
    }

    public function test_working_hours_with_plain_time_strings(): void
    {
        $this->assertEqualsWithDelta(
            9.0,
            $this->schedule('08:00', '17:00')->working_hours,
            0.001
        );
    }

    public function test_working_hours_with_full_datetime_strings_does_not_double_date(): void
    {
        // With the old concatenation this produced
        // "2026-07-14 2026-07-14 08:00:00" and threw.
        $this->assertEqualsWithDelta(
            9.0,
            $this->schedule('2026-07-14 08:00:00', '2026-07-14 17:00:00')->working_hours,
            0.001
        );
    }

    public function test_working_hours_zero_when_not_working(): void
    {
        $schedule = $this->schedule('08:00', '17:00');
        $schedule->status = 'Day Off';

        $this->assertSame(0.0, (float) $schedule->working_hours);
    }

    public function test_working_status_uses_planning_language_and_exposes_assignment_source(): void
    {
        $manual = $this->schedule('08:00', '17:00');
        $manual->created_by = 'user-id';

        $systemDefault = $this->schedule('08:00', '17:00');
        $systemDefault->notes = 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM.';

        $this->assertSame('Scheduled Workday', $manual->status_label);
        $this->assertSame('Manual', $manual->assignment_source);
        $this->assertSame('System Default', $systemDefault->assignment_source);
    }
}
