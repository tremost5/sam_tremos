<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // Run autopilot generation hourly to top-up inventory
        $schedule->command('autopilot:generate')->hourly()->withoutOverlapping();

        // Publish ready posts frequently
        $schedule->command('autopilot:publish')->everyFifteenMinutes()->withoutOverlapping();

        // Cleanup generated images daily
        $schedule->command('autopilot:cleanup')->dailyAt('03:00')->withoutOverlapping();
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
    }
}
