<?php

/**
 * Models swept by `php artisan ob:expire-overdue` (see
 * app/Console/Commands/ExpireOfficialBusinessRequests.php).
 *
 * Each entry must be a model class using the App\Models\Concerns\HasExpiryWindow
 * trait (i.e. has PENDING/EXPIRED constants, an `expires_at` column, and
 * scopePastDeadline()/isPastDeadline() from the trait).
 *
 * Currently only Official Business is in scope for this project. If/when
 * Leave requests get the same cutoff/expiry treatment, add
 * \App\Models\LeaveRequest::class here — no command code changes needed,
 * as long as LeaveRequest adopts HasExpiryWindow the same way
 * OfficialBusinessRequest does.
 */
return [
    'models' => [
        \App\Models\OfficialBusinessRequest::class,
    ],
];
