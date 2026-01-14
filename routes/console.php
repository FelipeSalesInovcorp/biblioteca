<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


Schedule::command('requisicoes:send-reminders')->dailyAt('15:00');

Schedule::command('carrinho:notificar-abandonados --hours=1')->everyTenMinutes()
->withoutOverlapping();