<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('bills:mark-overdue')->dailyAt('01:00');
Schedule::command('bills:remind')->dailyAt('07:00');
// Paket Genap 2026/2027 dibuka H-14 sebelum 1 Jan 2027 (idempotent).
Schedule::command('bills:generate-package', ['2026/2027', 'genap'])->dailyAt('02:00')->when(fn () => now()->toDateString() >= '2026-12-18');
