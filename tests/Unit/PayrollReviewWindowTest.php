<?php

namespace Tests\Unit;

use App\Models\Period;
use App\Notifications\PayrollReadyForReview;
use Carbon\Carbon;
use Tests\TestCase;

class PayrollReviewWindowTest extends TestCase
{
    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_review_window_is_pending_before_expiration(): void
    {
        Carbon::setTestNow('2026-08-14 09:00:00');

        $period = new Period();
        $period->forceFill([
            'status' => Period::STATUS_FOR_REVIEW,
            'review_notified_at' => now(),
            'review_expires_at' => now()->addHours(48),
            'reviewed_at' => null,
        ]);

        $this->assertTrue($period->isUnderReview());
        $this->assertFalse($period->reviewHasExpired());
        $this->assertSame('pending', $period->reviewState());
    }

    public function test_review_window_is_expired_after_48_hours_without_action(): void
    {
        Carbon::setTestNow('2026-08-16 09:00:01');

        $period = new Period();
        $period->forceFill([
            'status' => Period::STATUS_FOR_REVIEW,
            'review_notified_at' => now()->subHours(48)->subSecond(),
            'review_expires_at' => now()->subSecond(),
            'reviewed_at' => null,
        ]);

        $this->assertTrue($period->reviewHasExpired());
        $this->assertSame('expired', $period->reviewState());
    }

    public function test_reviewed_period_is_not_reported_as_expired(): void
    {
        Carbon::setTestNow('2026-08-16 09:00:01');

        $period = new Period();
        $period->forceFill([
            'status' => Period::STATUS_FOR_REVIEW,
            'review_notified_at' => now()->subHours(49),
            'review_expires_at' => now()->subHour(),
            'reviewed_at' => now()->subMinutes(30),
        ]);

        $this->assertFalse($period->reviewHasExpired());
        $this->assertSame('reviewed', $period->reviewState());
    }

    public function test_notification_contains_relative_review_url_and_expiration(): void
    {
        $notification = new PayrollReadyForReview(
            'period-123',
            'August 2026 - Period 1',
            '2026-08-16T09:00:00+08:00'
        );

        $payload = $notification->toArray(new \stdClass());

        $this->assertSame('period-123', $payload['period_id']);
        $this->assertSame('2026-08-16T09:00:00+08:00', $payload['review_expires_at']);
        $this->assertStringContainsString('period-123', $payload['url']);
        $this->assertStringStartsNotWith('http', $payload['url']);
    }
}
