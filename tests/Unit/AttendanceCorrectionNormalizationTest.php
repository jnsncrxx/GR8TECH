<?php

namespace Tests\Unit;

use App\Http\Controllers\Web\AttendanceController;
use App\Models\AttendanceRecord;
use ReflectionClass;
use ReflectionMethod;
use Tests\TestCase;

class AttendanceCorrectionNormalizationTest extends TestCase
{
    public function test_absent_correction_discards_all_punch_and_break_values(): void
    {
        $values = $this->normalize([
            'employee_id' => 'employee-id',
            'date' => '2026-08-01',
            'status' => AttendanceRecord::ABSENT,
            'time_in' => '20:50',
            'time_out' => '21:30',
            'break_start' => '12:00',
            'break_end' => '13:00',
            'notes' => 'Confirmed absence',
        ]);

        $this->assertNull($values['time_in']);
        $this->assertNull($values['time_out']);
        $this->assertNull($values['break_start']);
        $this->assertNull($values['break_end']);
        $this->assertSame(AttendanceRecord::ABSENT, $values['status']);
    }

    public function test_non_absent_correction_preserves_valid_punches(): void
    {
        $values = $this->normalize([
            'employee_id' => 'employee-id',
            'date' => '2026-08-01',
            'status' => AttendanceRecord::LATE,
            'time_in' => '08:50',
            'time_out' => '17:00',
            'break_start' => '12:00',
            'break_end' => '13:00',
        ]);

        $this->assertSame('2026-08-01 08:50:00', $values['time_in']->format('Y-m-d H:i:s'));
        $this->assertSame('2026-08-01 17:00:00', $values['time_out']->format('Y-m-d H:i:s'));
        $this->assertSame('2026-08-01 12:00:00', $values['break_start']->format('Y-m-d H:i:s'));
        $this->assertSame('2026-08-01 13:00:00', $values['break_end']->format('Y-m-d H:i:s'));
    }

    private function normalize(array $validated): array
    {
        $method = new ReflectionMethod(AttendanceController::class, 'normalizedAttendanceCorrection');
        $method->setAccessible(true);

        $controller = (new ReflectionClass(AttendanceController::class))->newInstanceWithoutConstructor();

        return $method->invoke($controller, $validated);
    }
}
