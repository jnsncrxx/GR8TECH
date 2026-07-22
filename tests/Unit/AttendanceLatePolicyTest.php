<?php

namespace Tests\Unit;

use App\Models\AttendanceRecord;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class AttendanceLatePolicyTest extends TestCase
{
    public function test_ten_minutes_is_within_grace(): void
    {
        $this->assertSame(0, $this->lateMinutes('08:00:00', '08:10:00'));
    }

    public function test_eleven_minutes_deducts_the_complete_lateness(): void
    {
        $this->assertSame(11, $this->lateMinutes('08:00:00', '08:11:00'));
    }

    private function lateMinutes(string $scheduled, string $actual): int
    {
        $method = new ReflectionMethod(AttendanceRecord::class, 'lateMinutesAfterGrace');
        $method->setAccessible(true);

        return $method->invoke(null, $scheduled, $actual);
    }
}
