<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Backup automático todos los días a las 23:00
Schedule::command('db:backup')->dailyAt('23:00');

// También al mediodía para mayor seguridad
Schedule::command('db:backup')->dailyAt('12:00');
