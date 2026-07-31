<?php

namespace Tests\Unit;

use App\Models\Period;
use Carbon\Carbon;
use Tests\TestCase;

class PeriodDeadlineTest extends TestCase
{
    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_deadline_states_are_reported_consistently(): void
    {
        Carbon::setTestNow('2026-07-26 12:00:00');

        $period = new Period([
            'request_deadline_at' => '2026-07-26 11:59:59',
            'preparation_deadline_at' => '2026-07-27 08:00:00',
            'validation_deadline_at' => '2026-07-29 23:59:59',
        ]);

        $this->assertTrue($period->deadlineHasPassed('request_deadline_at'));
        $this->assertSame('overdue', $period->deadlineState('request_deadline_at'));
        $this->assertSame('due_soon', $period->deadlineState('preparation_deadline_at'));
        $this->assertSame('open', $period->deadlineState('validation_deadline_at'));
        $this->assertSame('not_set', $period->deadlineState('lock_deadline_at'));
    }
}
