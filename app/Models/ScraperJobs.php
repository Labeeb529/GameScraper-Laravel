<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScraperJobs extends Model
{
    protected $table = 'scraper_jobs';

    protected $fillable = [
        'name',
        'scraper_type',
        'url',
        'cron_expression',
        'active',
        'run_once',
        'last_run_at',
        'last_status',
        'last_rows_updated',
        'last_error',
    ];

    protected $casts = [
        'active' => 'boolean',
        'run_once' => 'boolean',
        'last_run_at' => 'datetime',
        'last_rows_updated' => 'integer',
    ];
}
