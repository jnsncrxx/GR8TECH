<?php

namespace App\Models\Concerns;

use Carbon\Carbon;

/**
 * Adds cutoff/grace-deadline expiry behavior to any request model that has:
 *   - a `status` column with PENDING and EXPIRED constants defined on the model
 *   - an `expires_at` nullable datetime column (set at filing time, typically
 *     via CutoffPeriodService::graceDeadlineFor())
 *
 * Built for OfficialBusinessRequest. Intended to be reusable as-is for any
 * other approval-style request model (e.g. LeaveRequest) that needs the same
 * "pending past its grace deadline -> expired" behavior — just `use` this
 * trait, add an `expires_at` column, and define PENDING/EXPIRED constants.
 * No other changes to this trait should be needed for that.
 */
trait HasExpiryWindow
{
    /**
     * True once a pending request is past its grace deadline but the expiry
     * sweep hasn't flipped its status yet. Useful for showing "Expired" in
     * the UI even between sweep runs, without mutating state on a read.
     */
    public function isPastDeadline(): bool
    {
        return $this->status === static::PENDING
            && $this->expires_at
            && Carbon::now()->greaterThan($this->expires_at);
    }

    /**
     * Pending requests whose grace deadline has passed — the set an expiry
     * sweep command should flip to EXPIRED.
     */
    public function scopePastDeadline($query)
    {
        return $query->where('status', static::PENDING)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', Carbon::now());
    }
}
