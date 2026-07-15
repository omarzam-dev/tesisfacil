<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Revisa todos los días a las 9am quién vence en 3 días y le manda el aviso
Schedule::command('tesisfacil:recordatorios-vencimiento')->dailyAt('09:00');
