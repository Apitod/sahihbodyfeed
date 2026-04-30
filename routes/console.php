<?php

use App\Console\Commands\ProcessDailyCommissions;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| Define Artisan commands and scheduled tasks here.
|
| DEPLOYMENT NOTE:
|   Add this line to your server's crontab so Laravel's scheduler runs:
|   * * * * * cd /path/to/sahihbodyfeed && php artisan schedule:run >> /dev/null 2>&1
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ── Commission Processing Schedule ─────────────────────────────────────────
// Runs every day at 01:00 WITA (Asia/Makassar = UTC+8).
// This accumulates all yesterday's `menunggu` commissions → `pending`,
// making them visible in the admin's disbursement / PDF report queue.
Schedule::command(ProcessDailyCommissions::class)
    ->dailyAt('01:00')
    ->timezone('Asia/Makassar')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/commission-schedule.log'));
