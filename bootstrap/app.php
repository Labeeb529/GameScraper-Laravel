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
            $timezone = config('app.timezone', 'UTC');
            
            Log::info('[Scheduler] Running job check', [
                'time' => $now->format('Y-m-d H:i:s'),
                'timezone' => $timezone
            ]);
            
            $activeJobs = ScraperJobs::where('active', true)->get();
            Log::info('[Scheduler] Active jobs found', ['count' => $activeJobs->count()]);
            
            $activeJobs->each(function ($job) use ($now) {
                $shouldRun = shouldRunCronJob($job->cron_expression, $now);
                
                Log::debug('[Scheduler] Job evaluation', [
                    'job_id' => $job->id,
                    'name' => $job->name,
                    'cron' => $job->cron_expression,
                    'should_run' => $shouldRun
                ]);
                
                if ($shouldRun) {
                    Log::info('[Scheduler] Executing job', [
                        'job_id' => $job->id,
                        'name' => $job->name
                    ]);
                    
                    try {
                        $exitCode = Artisan::call('scraper:run', [
                            'job_id' => $job->id
                        ]);
                        
                        Log::info('[Scheduler] Job completed', [
                            'job_id' => $job->id,
                            'exit_code' => $exitCode
                        ]);
                    } catch (\Throwable $e) {
                        Log::error('[Scheduler] Job execution failed', [
                            'job_id' => $job->id,
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString()
                        ]);
                    }
                }
            });
        })->everyMinute()->timezone('America/Denver');
    })
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
