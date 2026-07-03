<?php
use Illuminate\Support\Facades\Schedule;

Schedule::command('cheque:reminder')->dailyAt('20:03');
Schedule::command('backup:daily')
    ->dailyAt('02:00')
    ->withoutOverlapping()
    ->onOneServer();