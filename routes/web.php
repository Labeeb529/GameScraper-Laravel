<?php

use App\Http\Controllers\BetsController;
use App\Enums\GameType;
use App\Http\Controllers\DataController;
use App\Http\Controllers\ScrapeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/scrape-bets/{game}', [ScrapeController::class, 'scrapeBets']);
Route::get('/scrape-results/{game}', [ScrapeController::class, 'scrapeResults']);

// Cron Management Routes
Route::get('/cron-management', function () {
    return view('cron');
});

Route::get('/data/{game}/{type}', [DataController::class, 'getData'])
    ->name('data')
    ->where('type', 'bets|results');


//API call for CSV exports

Route::get('/data/export', [DataController::class, 'exportCsv'])
    ->name('export-data')
    ->where('type', 'bets|results');

Route::get('/data/export/download/{file}', [DataController::class, 'downloadExport'])
    ->name('export.download');

Route::get('/data/indices', [DataController::class, 'getIndices'])
    ->name('data.indices');
