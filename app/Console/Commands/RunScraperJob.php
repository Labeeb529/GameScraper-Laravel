<?php

namespace App\Console\Commands;

use App\Models\ScraperJobs;
use App\Services\ScrapingService;
use Exception;
use Illuminate\Console\Command;

class RunScraperJob extends Command
{
    protected $signature = 'scraper:run {job_id}';

    protected $description = 'Run a scraper job by ID';

    public function handle(ScrapingService $scrapingService)
    {
        $job = ScraperJobs::find($this->argument('job_id'));

        if (!$job || !$job->active) {
            return 0;
        }

        $job->update([
            'last_run_at' => now(),
            'last_status' => null,
            'last_error' => null,
            'last_rows_updated' => null,
        ]);

        try {
            $scraperType = $job->scraper_type;
            $url = $job->url;

            if (str_contains($scraperType, '-bets')) {
                $game = str_replace('-bets', '', $scraperType);
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
                
                $rowsUpdated = $scrapingService->scrapeResults($game, $url, $date, $week, $year);
            } else {
                throw new Exception('Unknown scraper type: ' . $scraperType);
            }

            $job->update([
                'last_status' => 'success',
                'last_rows_updated' => $rowsUpdated,
            ]);

            if ($job->run_once) {
                $job->update(['active' => false]);
            }
        } catch (\Throwable $e) {
            $job->update([
                'last_status' => 'failed',
                'last_error' => $e->getMessage(),
            ]);
            
            $this->error('Scraper job failed: ' . $e->getMessage());
        }

        return 0;
    }
}
