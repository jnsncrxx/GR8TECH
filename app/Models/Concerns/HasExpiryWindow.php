<?php

namespace App\Models\Concerns;

use Carbon\Carbon;

/**
 * Adds a shared two-stage, 24-hours-per-stage expiry workflow to a request
 * model that has:
 *   - a `status` column with PENDING and EXPIRED constants defined on the model
 *   - the expiry columns introduced by the two-stage expiry migration
 *
 * Built for OfficialBusinessRequest. Intended to be reusable as-is for any
 * other approval-style request model (e.g. LeaveRequest) that needs the same
 * "pending past its grace deadline -> expired" behavior — just `use` this
 * trait, add an `expires_at` column, and define PENDING/EXPIRED constants.
 * No other changes to this trait should be needed for that.
 */
trait HasExpiryWindow
{
    public const EXPIRY_WINDOW_HOURS = 24;
    public const FINAL_EXPIRY_ATTEMPT = 2;

    public static function bootHasExpiryWindow(): void
    {
        static::creating(function ($request) {
            $request->expiry_attempt = $request->expiry_attempt ?: 1;
            $request->expires_at = $request->expires_at ?: Carbon::now()->addHours(self::EXPIRY_WINDOW_HOURS);
        });
    }

    /**
     * True once a pending request is past its grace deadline but the expiry
     * sweep hasn't flipped its status yet. Useful for showing "Expired" in
     * the UI even between sweep runs, without mutating state on a read.
     */
    public function isPastDeadline(): bool
    {
        return $this->status === static::PENDING
            && $this->expires_at
            && Carbon::now()->greaterThanOrEqualTo($this->expires_at);
    }

    /**
     * Pending requests whose grace deadline has passed — the set an expiry
     * sweep command should flip to EXPIRED.
     */
    public function scopePastDeadline($query)
    {
        return $query->where('status', static::PENDING)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', Carbon::now());
    }

    public function canBeResubmitted(): bool
    {
        return $this->status === static::EXPIRED
            && (int) $this->expiry_attempt === 1
            && $this->first_expired_at !== null
            && $this->final_expired_at === null;
    }

    public function isFinallyExpired(): bool
    {
        return $this->status === static::EXPIRED
            && ((int) $this->expiry_attempt >= self::FINAL_EXPIRY_ATTEMPT || $this->final_expired_at !== null);
    }

    /** Mark an overdue pending request expired and retain which window ended. */
    public function expireCurrentWindow(): bool
    {
        if (!$this->isPastDeadline()) {
            return false;
        }

        $isFinal = (int) $this->expiry_attempt >= self::FINAL_EXPIRY_ATTEMPT;
        $this->forceFill([
            'status' => static::EXPIRED,
            'first_expired_at' => $isFinal ? $this->first_expired_at : Carbon::now(),
            'final_expired_at' => $isFinal ? Carbon::now() : null,
        ])->save();

        return true;
    }

    /** Reopen the same filing for its one permitted final review window. */
    public function resubmitForFinalWindow(): bool
    {
        if (!$this->canBeResubmitted()) {
            return false;
        }

        $this->forceFill([
            'status' => static::PENDING,
            'expiry_attempt' => self::FINAL_EXPIRY_ATTEMPT,
            'resubmitted_at' => Carbon::now(),
            'expires_at' => Carbon::now()->addHours(self::EXPIRY_WINDOW_HOURS),
        ])->save();

        return true;
    }
}
