<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use Illuminate\Support\Facades\Schedule;

// Le decimos a Laravel que ejecute nuestro comando cada minuto
Schedule::command('tickets:cancelar-vencidos')->everyMinute();