<?php

use App\Models\ScraperJobs;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

require_once __DIR__ . '/../app/Helpers/CronHelper.php';

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )->withSchedule(function (Schedule $schedule) {
        $schedule->call(function () {
            $now = now();
            ScraperJobs::where('active', true)->each(function ($job) use ($now) {
                if (shouldRunCronJob($job->cron_expression, $now)) {
                    Artisan::call('scraper:run', [
                        'job_id' => $job->id
                    ]);
                }
            });
        })->everyMinute();
    })
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
