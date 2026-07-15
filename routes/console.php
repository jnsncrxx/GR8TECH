<?php
use Illuminate\Support\Facades\Schedule;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('ob:expire-overdue')->everyFifteenMinutes();

// mark absent employees after shift ends (5pm)
Schedule::command('attendance:mark-absent')->dailyAt('17:15');