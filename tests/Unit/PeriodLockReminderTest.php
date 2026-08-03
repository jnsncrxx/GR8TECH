<?php

namespace Tests\Unit;

use App\Models\Period;
use Carbon\Carbon;
use Tests\TestCase;

class PeriodLockReminderTest extends TestCase
{
    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_past_unlocked_period_needs_a_manual_lock_reminder(): void
    {
        Carbon::setTestNow('2026-08-03 12:00:00');

        $period = new Period([
            'end_date' => '2026-08-02',
            'status' => Period::STATUS_FINALIZED,
        ]);

        $this->assertTrue($period->needsLockReminder());
    }

    public function test_current_or_locked_period_does_not_need_a_reminder(): void
    {
        Carbon::setTestNow('2026-08-03 12:00:00');

        $current = new Period([
            'end_date' => '2026-08-03',
            'status' => Period::STATUS_OPEN,
        ]);
        $locked = new Period([
            'end_date' => '2026-08-02',
            'status' => Period::STATUS_LOCKED,
        ]);

        $this->assertFalse($current->needsLockReminder());
        $this->assertFalse($locked->needsLockReminder());
    }
}
