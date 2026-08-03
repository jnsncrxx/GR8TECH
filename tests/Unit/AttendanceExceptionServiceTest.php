<?php

namespace Tests\Unit;

use App\Models\AttendanceRecord;
use App\Models\EmployeeSchedule;
use App\Services\AttendanceExceptionService;
use Tests\TestCase;

class AttendanceExceptionServiceTest extends TestCase
{
    public function test_approved_leave_with_attendance_is_a_conflict(): void
    {
        $issues = $this->service()->evaluate($this->completeRecord(), $this->workingSchedule(), 8, true);

        $this->assertContains('leave_conflict', array_column($issues, 'code'));
    }

    public function test_approved_overtime_without_complete_attendance_is_blocking(): void
    {
        $record = $this->record(['time_out' => null]);
        $issues = $this->service()->evaluate($record, $this->workingSchedule(), 0, false, false, 2);

        $this->assertContains('ot_without_attendance', array_column($issues, 'code'));
    }

    public function test_overtime_before_required_hours_is_blocking(): void
    {
        $issues = $this->service()->evaluate($this->completeRecord(), $this->workingSchedule(), 4, false, false, 2);

        $this->assertContains('ot_before_required_hours', array_column($issues, 'code'));
    }

    public function test_approved_leave_without_conflicting_activity_is_clear(): void
    {
        $record = $this->record([
            'status' => AttendanceRecord::ON_LEAVE,
            'time_in' => null,
            'time_out' => null,
        ]);
        $service = $this->service();
        $issues = $service->evaluate($record, $this->workingSchedule(), 0, true);

        $this->assertSame([], $issues);
        $this->assertSame('Approved leave', $service->primary($issues, true, false, $record)['label']);
    }

    private function service(): AttendanceExceptionService
    {
        return app(AttendanceExceptionService::class);
    }

    private function completeRecord(): AttendanceRecord
    {
        return $this->record();
    }

    private function record(array $overrides = []): AttendanceRecord
    {
        $record = new AttendanceRecord(array_merge([
            'date' => '2026-08-01',
            'status' => AttendanceRecord::PRESENT,
            'time_in' => '2026-08-01 08:00:00',
            'time_out' => '2026-08-01 17:00:00',
        ], $overrides));
        $record->setRelation('timeEntries', collect());

        return $record;
    }

    private function workingSchedule(): EmployeeSchedule
    {
        return new EmployeeSchedule([
            'date' => '2026-08-01',
            'status' => 'Working',
            'time_in' => '08:00:00',
            'time_out' => '17:00:00',
            'required_hours' => 8,
        ]);
    }
}
