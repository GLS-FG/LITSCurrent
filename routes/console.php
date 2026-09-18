<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('app:create-daily-notifications')->dailyAt('8:00');
