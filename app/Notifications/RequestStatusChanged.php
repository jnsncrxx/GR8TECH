<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

/**
 * Fired whenever a Leave, Overtime, or Official Business request moves out
 * of "pending" into Approved, Rejected, or Expired. Stored in the database
 * (see notifications table / migration 2026_07_18_000000) and read back via
 * $account->notifications() / $account->unreadNotifications() for the
 * in-app bell in the dashboard header.
 *
 * This is DB-only (in-app) for now. Email delivery was flagged as an open
 * question on the originating ticket ("confirm whether email notifications
 * are in scope, or in-app only"); if that gets confirmed later, add
 * `use Illuminate\Notifications\Notifiable;` `via()` => ['database', 'mail']
 * and a toMail() method here — no other changes needed since Account
 * already has an `email` column and uses Notifiable.
 */
class RequestStatusChanged extends Notification
{
    public const TYPE_LEAVE = 'leave';
    public const TYPE_OVERTIME = 'overtime';
    public const TYPE_OFFICIAL_BUSINESS = 'official_business';

    private const TYPE_LABELS = [
        self::TYPE_LEAVE => 'Leave Request',
        self::TYPE_OVERTIME => 'Overtime Request',
        self::TYPE_OFFICIAL_BUSINESS => 'Official Business Request',
    ];

    private const STATUS_LABELS = [
        'pending' => 'Pending',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
        'expired' => 'Expired',
    ];

    private const STATUS_ICONS = [
        'approved' => 'fa-check-circle',
        'rejected' => 'fa-times-circle',
        'expired' => 'fa-hourglass-end',
    ];

    private const STATUS_COLORS = [
        'approved' => 'green',
        'rejected' => 'red',
        'expired' => 'gray',
    ];

    public function __construct(
        public string $requestType,      // one of the TYPE_* constants
        public string $requestId,
        public string $status,           // 'approved' | 'rejected' | 'expired'
        public string $dateLabel,        // human-readable date/range for the request
        public ?string $rejectionReason = null,
        public bool $finalExpiration = false,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $typeLabel = self::TYPE_LABELS[$this->requestType] ?? 'Request';
        $statusLabel = self::STATUS_LABELS[$this->status] ?? ucfirst($this->status);

        $message = "Your {$typeLabel} for {$this->dateLabel} was {$statusLabel}.";
        if ($this->status === 'rejected' && $this->rejectionReason) {
            $message .= " Reason: {$this->rejectionReason}";
        }
        if ($this->status === 'expired') {
            $message = $this->finalExpiration
                ? "Your resubmitted {$typeLabel} for {$this->dateLabel} has finally expired. It can no longer be re-requested."
                : "Your {$typeLabel} for {$this->dateLabel} expired before it was reviewed. You may re-request it once for a final 24-hour review period.";
        }

        return [
            'request_type' => $this->requestType,
            'request_type_label' => $typeLabel,
            'request_id' => $this->requestId,
            'status' => $this->status,
            'status_label' => $statusLabel,
            'date' => $this->dateLabel,
            'title' => "{$typeLabel} {$statusLabel}",
            'message' => $message,
            'icon' => self::STATUS_ICONS[$this->status] ?? 'fa-bell',
            'color' => self::STATUS_COLORS[$this->status] ?? 'blue',
            'url' => $this->urlFor($this->requestType),
            'final_expiration' => $this->finalExpiration,
        ];
    }

    private function urlFor(string $requestType): string
    {
        return match ($requestType) {
            self::TYPE_LEAVE => route('attendance.leave-management'),
            self::TYPE_OVERTIME => route('attendance.overtime'),
            self::TYPE_OFFICIAL_BUSINESS => route('attendance.official-business'),
            default => '/',
        };
    }
}
