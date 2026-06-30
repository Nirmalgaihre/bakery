<?php
use Illuminate\Support\Facades\Schedule;

Schedule::command('cheques:send-reminders')->dailyAt('09:52');

Schedule::command('backup:daily')
    ->dailyAt('02:00')
    ->withoutOverlapping()
    ->onOneServer();