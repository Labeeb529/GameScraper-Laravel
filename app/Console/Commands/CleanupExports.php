<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanupExports extends Command
{
    protected $signature = 'exports:cleanup {--days=7 : Number of days to keep exports}';

    protected $description = 'Clean up old export files';

    public function handle()
    {
        $days = (int) $this->option('days');
        $cutoffDate = now()->subDays($days);
        $exportDir = storage_path('app/exports');

        if (!is_dir($exportDir)) {
            $this->info('Export directory does not exist.');
            return 0;
        }

        $files = glob($exportDir . DIRECTORY_SEPARATOR . '*.csv');
        $deleted = 0;
        $errors = 0;

        foreach ($files as $file) {
            if (file_exists($file)) {
                $fileTime = filemtime($file);
                if ($fileTime !== false && $fileTime < $cutoffDate->timestamp) {
                    if (@unlink($file)) {
                        $deleted++;
                    } else {
                        $errors++;
                        $this->warn('Failed to delete: ' . basename($file));
                    }
                }
            }
        }

        $this->info("Cleaned up {$deleted} export file(s).");
        if ($errors > 0) {
            $this->warn("Failed to delete {$errors} file(s).");
        }

        return 0;
    }
}


