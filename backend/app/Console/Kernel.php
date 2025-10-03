<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     * Deconnecter les utilisateurs a partir de 18h30 
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('app:deconnect-user')->weekdays()->dailyAt('18:30');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
