<?php
use Illuminate\Support\Facades\Schedule;

Schedule::command('cheques:send-reminders')->dailyAt('21:45');