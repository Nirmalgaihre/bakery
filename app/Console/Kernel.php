<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\SendChequeReminder::class,
        \App\Console\Commands\RunDailyBackup::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Cheque reminder at 8:00 AM daily
        $schedule->command('cheque:reminder')
            ->dailyAt('08:00')
            ->withoutOverlapping()
            ->onOneServer();

        // Daily backup at 8:00 PM (20:00) daily
        $schedule->command('backup:daily')
            ->dailyAt('20:00')
            ->withoutOverlapping()
            ->onOneServer();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}