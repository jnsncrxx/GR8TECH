<?php

namespace Tests\Unit;

use App\Http\Controllers\Web\PeriodManagementController;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;

class PeriodManagementCutoffTest extends TestCase
{
    public function test_standard_period_one_dates_are_derived_from_pay_month(): void
    {
        $this->assertSame([
            'start_date' => '2026-06-26',
            'end_date' => '2026-07-10',
            'payroll_date' => '2026-07-15',
        ], $this->invoke('standardPeriodDates', 2026, 7, 1));
    }

    public function test_standard_period_two_dates_are_derived_from_pay_month(): void
    {
        $this->assertSame([
            'start_date' => '2026-07-11',
            'end_date' => '2026-07-25',
            'payroll_date' => '2026-07-30',
        ], $this->invoke('standardPeriodDates', 2026, 7, 2));
    }

    public function test_working_day_count_excludes_only_sunday(): void
    {
        $this->assertSame(
            12,
            $this->invoke('countWeekdays', Carbon::parse('2026-07-05'), Carbon::parse('2026-07-19'))
        );
    }

    private function invoke(string $method, mixed ...$arguments): mixed
    {
        $reflection = new ReflectionMethod(PeriodManagementController::class, $method);
        $reflection->setAccessible(true);
        $controller = (new ReflectionClass(PeriodManagementController::class))
            ->newInstanceWithoutConstructor();

        return $reflection->invoke($controller, ...$arguments);
    }
}
