<?php

namespace App\Console\Commands;

use App\Models\ScraperJobs;
use App\Services\ScrapingService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RunScraperJob extends Command
{
    protected $signature = 'scraper:run {job_id}';

    protected $description = 'Run a scraper job by ID';

    public function handle(ScrapingService $scrapingService)
    {
        $jobId = $this->argument('job_id');
        Log::info('[RunScraperJob] Starting', ['job_id' => $jobId]);
        
        $job = ScraperJobs::find($jobId);

        if (!$job) {
            Log::warning('[RunScraperJob] Job not found', ['job_id' => $jobId]);
            $this->error("Job ID $jobId not found");
            return 1;
        }

        if (!$job->active) {
            Log::info('[RunScraperJob] Job is inactive, skipping', ['job_id' => $jobId]);
            return 0;
        }

        Log::info('[RunScraperJob] Executing job', [
            'job_id' => $job->id,
            'name' => $job->name,
            'type' => $job->scraper_type,
            'url' => $job->url
        ]);

        $job->update([
            'last_run_at' => now(),
            'last_status' => 'running',
            'last_error' => null,
            'last_rows_updated' => null,
        ]);

        try {
            $scraperType = $job->scraper_type;
            $url = $job->url;
            $rowsUpdated = 0;

            if (str_contains($scraperType, '-bets')) {
                $game = str_replace('-bets', '', $scraperType);
                Log::info('[RunScraperJob] Running bets scraper', ['game' => $game]);
                $rowsUpdated = $scrapingService->scrapeBets($game, $url);
            } elseif (str_contains($scraperType, '-results')) {
                $game = str_replace('-results', '', $scraperType);
                
                $date = null;
                $week = null;
                $year = null;
                
                if ($game === 'ncaab') {
                    if (preg_match('/[?&]date=([^&]+)/', $url, $matches)) {
                        $date = urldecode($matches[1]);
                    }
                } else {
                    if (preg_match('/[?&]week=(\d+)-reg-(\d+)/', $url, $matches)) {
                        $year = $matches[1];
                        $week = $matches[2];
                    }
                }
                
                Log::info('[RunScraperJob] Running results scraper', [
                    'game' => $game,
                    'date' => $date,
                    'week' => $week,
                    'year' => $year
                ]);
                $rowsUpdated = $scrapingService->scrapeResults($game, $url, $date, $week, $year);
            } else {
                throw new Exception('Unknown scraper type: ' . $scraperType);
            }

            Log::info('[RunScraperJob] Completed successfully', [
                'job_id' => $job->id,
                'rows_updated' => $rowsUpdated
            ]);

            $job->update([
                'last_status' => 'success',
                'last_rows_updated' => $rowsUpdated,
            ]);

            if ($job->run_once) {
                $job->update(['active' => false]);
                Log::info('[RunScraperJob] One-time job deactivated', ['job_id' => $job->id]);
            }

            $this->info("Job completed: $rowsUpdated rows updated");
        } catch (\Throwable $e) {
            Log::error('[RunScraperJob] Job failed', [
                'job_id' => $job->id,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            $job->update([
                'last_status' => 'failed',
                'last_error' => $e->getMessage(),
            ]);
            
            $this->error('Scraper job failed: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
