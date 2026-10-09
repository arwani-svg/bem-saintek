<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Jaga tabel activity_logs tetap ringan: hapus log > 90 hari tiap Senin pagi.
Schedule::command('activity-log:prune')->weeklyOn(1, '03:00');
