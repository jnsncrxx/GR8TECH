<?php

// NOTE: If your Period Management module (app/Http/Controllers/Web/PeriodManagementController.php)
// already stores cutoff date ranges in a `periods` table, prefer that as the source of
// truth and point CutoffPeriodService at it instead of this config. This file exists so
// OB filing/approval has a working cutoff definition without depending on that answer.

return [

    // Day-of-month cutoff boundaries. A "period" runs from the day after the previous
    // cutoff day up to and including the next cutoff day.
    // With [10, 25]: periods each month are (26th -> 10th) and (11th -> 25th).
    'cutoff_days' => [10, 25],

    // Hours after a cutoff period ends during which a still-pending OB request can
    // still be approved before it auto-expires.
    'grace_period_hours' => 24,

];
