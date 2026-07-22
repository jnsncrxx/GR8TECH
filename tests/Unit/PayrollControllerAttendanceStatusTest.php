<?php

namespace Tests\Unit;

use App\Http\Controllers\Web\PayrollController;
use App\Models\AttendanceRecord;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;

class PayrollControllerAttendanceStatusTest extends TestCase
{
    public function test_approved_official_business_takes_precedence_over_missing_attendance(): void
    {
        $method = new ReflectionMethod(PayrollController::class, 'getAttendanceStatus');
        $method->setAccessible(true);
        $controller = (new ReflectionClass(PayrollController::class))
            ->newInstanceWithoutConstructor();

        $status = $method->invoke($controller, null, new \stdClass(), true);

        $this->assertSame('Official Business', $status);
    }

    public function test_status_uses_calculated_hours_instead_of_stale_stored_total(): void
    {
        $attendance = new class {
            public string $status = AttendanceRecord::PRESENT;
            public string $time_in = '2026-07-09 15:14:00';
            public string $time_out = '2026-07-09 19:15:00';
            public float $total_hours = 0.0;

            public function calculateTotalHours(): float
            {
                return 4.02;
            }

            public function isLate(): bool
            {
                return true;
            }
        };

        $method = new ReflectionMethod(PayrollController::class, 'getAttendanceStatus');
        $method->setAccessible(true);

        $controller = (new ReflectionClass(PayrollController::class))
            ->newInstanceWithoutConstructor();
        $status = $method->invoke($controller, $attendance, new \stdClass(), false);

        $this->assertSame('Late', $status);
    }
}
