<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Ensure scheduler entries are available even if Console Kernel wiring differs
$schedule = app(Illuminate\Console\Scheduling\Schedule::class);
$schedule->command('autopilot:generate')->hourly()->withoutOverlapping();
$schedule->command('autopilot:publish')->everyFifteenMinutes()->withoutOverlapping();
$schedule->command('autopilot:cleanup')->dailyAt('03:00')->withoutOverlapping();
