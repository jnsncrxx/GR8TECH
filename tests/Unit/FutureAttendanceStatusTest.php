<?php

namespace Tests\Unit;

use App\Http\Controllers\Web\AttendanceController;
use Carbon\Carbon;
use ReflectionMethod;
use Tests\TestCase;

class FutureAttendanceStatusTest extends TestCase
{
    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_only_dates_after_today_are_treated_as_future_attendance_dates(): void
    {
        Carbon::setTestNow('2026-08-01 10:00:00');
        $method = new ReflectionMethod(AttendanceController::class, 'isFutureAttendanceDate');
        $method->setAccessible(true);
        $controller = app(AttendanceController::class);

        $this->assertFalse($method->invoke($controller, Carbon::parse('2026-07-31')));
        $this->assertFalse($method->invoke($controller, Carbon::parse('2026-08-01')));
        $this->assertTrue($method->invoke($controller, Carbon::parse('2026-08-04')));
    }
}
