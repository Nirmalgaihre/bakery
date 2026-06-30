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
];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
protected function schedule(Schedule $schedule)
{
    // First command
    $schedule->command('cheque:reminder')
        ->dailyAt('07:00');

    // Second command
    $schedule->command('backup:daily')
        ->dailyAt('19:00')
        ->withoutOverlapping();
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