<?php

use Illuminate\Support\Facades\Schedule;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Auto-expire requests
Schedule::command('leave:expire-overdue')->everyFifteenMinutes();
Schedule::command('overtime:expire-overdue')->everyFifteenMinutes();
Schedule::command('ob:expire-overdue')->everyFifteenMinutes();

// Mark absent employees after shift ends (5 PM)
Schedule::command('attendance:mark-absent')->dailyAt('17:15');