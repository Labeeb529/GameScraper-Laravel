<?php

namespace App\Services;

use App\Models\DataIndex;

class DataIndexService
{
    protected ?DataIndex $index = null;

    public function start(string $scraperName, $date = null, $week = null, $year = null): DataIndex
    {
        $this->index = DataIndex::create([
            'scraper_name' => $scraperName,
            'scraped_at' => now(),
            'rows_found' => 0,
            'rows_merged' => 0,
            'date' => $date,
            'week' => $week,
            'year' => $year,
        ]);

        return $this->index;
    }

    public function incrementFound(int $count = 1): void
    {
        if ($this->index === null) {
            return;
        }

        $this->index->increment('rows_found', $count);
    }

    public function incrementMerged(int $count = 1): void
    {
        if ($this->index === null) {
            return;
        }

        $this->index->increment('rows_merged', $count);
    }

    public function finish(): void
    {
        if ($this->index === null) {
            return;
        }

        $this->index->update([
            'completed_at' => now(),
        ]);
    }
}