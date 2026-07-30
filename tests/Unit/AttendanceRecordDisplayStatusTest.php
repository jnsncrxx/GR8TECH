<?php

namespace Tests\Unit;

use App\Models\AttendanceRecord;
use Carbon\Carbon;
use Tests\TestCase;

class AttendanceRecordDisplayStatusTest extends TestCase
{
    public function test_record_without_clock_out_is_labeled_incomplete_log(): void
    {
        $record = new AttendanceRecord([
            'time_in' => Carbon::parse('2026-07-28 08:00:00'),
            'time_out' => null,
            'status' => AttendanceRecord::PRESENT,
        ]);

        $this->assertSame('Incomplete Log', $record->display_status_label);
        $this->assertSame(
            AttendanceRecord::VALIDATION_INCOMPLETE_LOG,
            $record->derivePunchValidationStatus()
        );
    }

    public function test_completed_punch_uses_its_canonical_attendance_status(): void
    {
        $record = new AttendanceRecord([
            'time_in' => Carbon::parse('2026-07-28 08:00:00'),
            'time_out' => Carbon::parse('2026-07-28 17:00:00'),
            'status' => AttendanceRecord::PRESENT,
        ]);

        $this->assertSame('Present', $record->display_status_label);
        $this->assertSame(
            AttendanceRecord::VALIDATION_VALID,
            $record->derivePunchValidationStatus()
        );
    }
}
