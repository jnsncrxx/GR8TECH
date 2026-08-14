<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

/**
 * Fired when a payroll Period moves from Processing into For Review,
 * notifying the department's manager that calculated payroll is
 * awaiting their review within a 48-hour window. Same DB-only pattern
 * as RequestStatusChanged - see that class for the mail-channel note
 * if email delivery gets confirmed in scope later.
 */
class PayrollReadyForReview extends Notification
{
    public function __construct(
        public string $periodId,
        public string $periodName,
        public string $reviewExpiresAtIso,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'period_id' => $this->periodId,
            'period_name' => $this->periodName,
            'title' => 'Payroll Ready for Review',
            'message' => "Payroll for \"{$this->periodName}\" has been calculated and is ready for your review. You have 48 hours to review it.",
            'icon' => 'fa-money-check-dollar',
            'color' => 'amber',
            'review_expires_at' => $this->reviewExpiresAtIso,
            'url' => route('payroll.periods.review', $this->periodId, false),
        ];
    }
}