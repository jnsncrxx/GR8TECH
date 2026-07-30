<?php

namespace Tests\Unit;

use App\Http\Controllers\Web\AttendanceController;
use App\Http\Controllers\Web\PayrollController;
use App\Models\AttendanceRecord;
use App\Models\EmployeeSchedule;
use Carbon\Carbon;
use ReflectionClass;
use ReflectionMethod;
use Tests\TestCase;

class AttendanceExceptionResolutionTest extends TestCase
{
    public function test_reviewed_rest_day_duty_is_clear_in_timekeeping_and_payroll_validation(): void
    {
        $record = $this->attendanceRecord(AttendanceRecord::PRESENT, true);
        $schedule = $this->restDaySchedule();

        $timekeeping = $this->invokePrivate(
            AttendanceController::class,
            'timekeepingException',
            [$record, $schedule, 8.0]
        );
        $payrollIssue = $this->invokePrivate(
            PayrollController::class,
            'scheduleAttendanceValidationIssue',
            [$record, $schedule, false, false]
        );

        $this->assertSame('clear', $timekeeping['severity']);
        $this->assertSame('Rest-day duty reviewed', $timekeeping['label']);
        $this->assertNull($payrollIssue);
    }

    public function test_unreviewed_rest_day_duty_still_requires_review(): void
    {
        $record = $this->attendanceRecord(AttendanceRecord::PRESENT);
        $schedule = $this->restDaySchedule();

        $timekeeping = $this->invokePrivate(
            AttendanceController::class,
            'timekeepingException',
            [$record, $schedule, 8.0]
        );
        $payrollIssue = $this->invokePrivate(
            PayrollController::class,
            'scheduleAttendanceValidationIssue',
            [$record, $schedule, false, false]
        );

        $this->assertSame('review', $timekeeping['severity']);
        $this->assertSame('Rest Day Duty Review', $payrollIssue);
    }

    public function test_leave_and_official_business_markers_require_matching_approved_requests(): void
    {
        $schedule = $this->workingSchedule();

        foreach ([
            AttendanceRecord::ON_LEAVE => ['unverified_leave', 'Unverified Leave'],
            AttendanceRecord::OFFICIAL_BUSINESS => ['unverified_official_business', 'Unverified Official Business'],
        ] as $status => [$timekeepingCode, $payrollIssue]) {
            $record = $this->attendanceRecord($status);

            $timekeeping = $this->invokePrivate(
                AttendanceController::class,
                'timekeepingException',
                [$record, $schedule, 0.0, false, false]
            );
            $validation = $this->invokePrivate(
                PayrollController::class,
                'scheduleAttendanceValidationIssue',
                [$record, $schedule, false, false]
            );

            $this->assertSame($timekeepingCode, $timekeeping['code']);
            $this->assertSame('blocking', $timekeeping['severity']);
            $this->assertSame($payrollIssue, $validation);
        }
    }

    public function test_matching_approved_requests_clear_leave_and_official_business_markers(): void
    {
        $schedule = $this->workingSchedule();

        $leave = $this->attendanceRecord(AttendanceRecord::ON_LEAVE);
        $leaveResult = $this->invokePrivate(
            AttendanceController::class,
            'timekeepingException',
            [$leave, $schedule, 0.0, true, false]
        );

        $officialBusiness = $this->attendanceRecord(AttendanceRecord::OFFICIAL_BUSINESS);
        $officialBusinessResult = $this->invokePrivate(
            AttendanceController::class,
            'timekeepingException',
            [$officialBusiness, $schedule, 0.0, false, true]
        );

        $this->assertSame('clear', $leaveResult['severity']);
        $this->assertSame('Approved leave', $leaveResult['label']);
        $this->assertSame('clear', $officialBusinessResult['severity']);
        $this->assertSame('Approved official business', $officialBusinessResult['label']);
    }

    private function attendanceRecord(string $status, bool $reviewed = false): AttendanceRecord
    {
        $record = new AttendanceRecord([
            'date' => '2026-07-19',
            'status' => $status,
            'time_in' => '2026-07-19 08:00:00',
            'time_out' => '2026-07-19 17:00:00',
            'corrected_at' => $reviewed ? Carbon::parse('2026-07-20 09:00:00') : null,
        ]);
        $record->setRelation('timeEntries', collect());

        return $record;
    }

    private function restDaySchedule(): EmployeeSchedule
    {
        return new EmployeeSchedule([
            'date' => '2026-07-19',
            'status' => 'Day Off',
        ]);
    }

    private function workingSchedule(): EmployeeSchedule
    {
        return new EmployeeSchedule([
            'date' => '2026-07-19',
            'status' => 'Working',
            'time_in' => '08:00:00',
            'time_out' => '17:00:00',
        ]);
    }

    private function invokePrivate(string $class, string $method, array $arguments): mixed
    {
        $reflectionMethod = new ReflectionMethod($class, $method);
        $reflectionMethod->setAccessible(true);
        $instance = (new ReflectionClass($class))->newInstanceWithoutConstructor();

        return $reflectionMethod->invokeArgs($instance, $arguments);
    }
}
