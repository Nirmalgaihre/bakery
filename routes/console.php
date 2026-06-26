<?php
use Illuminate\Support\Facades\Schedule;
Schedule::command('cheques:send-reminders')->dailyAt('09:52');