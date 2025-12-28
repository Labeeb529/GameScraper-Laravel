<?php

use App\Http\Controllers\DataController;
use App\Http\Controllers\ScrapeController;
use App\Http\Controllers\SchedulingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/scrape-bets/{game}', [ScrapeController::class, 'scrapeBets'])
    ->where('game', 'nfl|ncaaf|ncaab');
Route::get('/scrape-results/{game}', [ScrapeController::class, 'scrapeResults'])
    ->where('game', 'nfl|ncaaf|ncaab');

// Scraper Job Scheduling Routes
Route::get('/cron-management', [SchedulingController::class, 'index'])->name('scraper-jobs.index');
Route::post('/scraper-jobs', [SchedulingController::class, 'store'])->name('scraper-jobs.store');
Route::patch('/scraper-jobs/{id}/toggle', [SchedulingController::class, 'toggle'])->name('scraper-jobs.toggle');
Route::delete('/scraper-jobs/{id}', [SchedulingController::class, 'destroy'])->name('scraper-jobs.destroy');

Route::get('/data/{game}/{type}', [DataController::class, 'getData'])
    ->name('data')
    ->where('game', 'nfl|ncaaf|ncaab')
    ->where('type', 'bets|results');


//API call for CSV exports

Route::get('/data/export', [DataController::class, 'exportCsv'])
    ->name('export-data')
    ->where('type', 'bets|results');

Route::get('/data/export/download/{file}', [DataController::class, 'downloadExport'])
    ->name('export.download');

Route::get('/data/indices', [DataController::class, 'getIndices'])
    ->name('data.indices');
