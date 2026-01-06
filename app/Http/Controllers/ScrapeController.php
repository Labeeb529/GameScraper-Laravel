<?php

namespace App\Http\Controllers;

use App\Services\ScrapingService;
use Illuminate\Http\Request;
use App\Enums\Game;

class ScrapeController extends Controller
{
    public function scrapeBets(Request $request, ScrapingService $scrapingService)
    {
        try {
            $gameValue = $request['game'] ?? '';
            
            if (empty($gameValue)) {
                return redirect()->back()->with('error', 'Game parameter is required');
            }

            Game::from($gameValue);

            $url = "https://www.scoresandodds.com/" . $gameValue . "/consensus-picks";
            $rowsUpdated = $scrapingService->scrapeBets($gameValue, $url);

            if ($rowsUpdated === 0) {
                return redirect()->back()->with('warning', 'No games found. The website may use JavaScript rendering or the page structure has changed.');
            }

            return redirect()->route('data', ['game' => $gameValue, 'type' => 'results'])->with('rows_updated', $rowsUpdated);
        } catch (\ValueError $e) {
            return redirect()->back()->with('error', 'Invalid game parameter');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'An error occurred while scraping: ' . $e->getMessage());
        }
        dd($e, 'Request Failed.');
    }


    public function scrapeResults(Request $request, ScrapingService $scrapingService)
    {
        try {
            $gameValue = $request['game'] ?? '';
            
            if (empty($gameValue)) {
                return redirect()->back()->with('error', 'Game parameter is required');
            }

            Game::from($gameValue);

            $date = $request->query('date');
            $year = $request->query('year');
            $week = $request->query('week');

            $url = '';
            if ($gameValue === "ncaab") {
                if (empty($date)) {
                    return redirect()->back()->with('error', 'Date parameter is required for NCAAB results');
                }
                $url = "https://www.scoresandodds.com/" . $gameValue . "?date=" . urlencode($date);
            } else {
                if (empty($year) || empty($week)) {
                    return redirect()->back()->with('error', 'Year and week parameters are required for ' . strtoupper($gameValue) . ' results');
                }
                $url = "https://www.scoresandodds.com/" . $gameValue . "?week=" . urlencode($year) . "-reg-" . urlencode($week);
            }

            $rowsUpdated = $scrapingService->scrapeResults($gameValue, $url, $date, $week, $year);

            if ($rowsUpdated === 0) {
                return redirect()->back()->with('warning', 'No final games found for the selected parameters');
            }

            return redirect()->route('data', ['game' => $gameValue, 'type' => 'results'])->with('rows_updated', $rowsUpdated);
        } catch (\ValueError $e) {
            return redirect()->back()->with('error', 'Invalid game parameter');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'An error occurred while scraping: ' . $e->getMessage());
        }
        dd($e, 'Request Failed.');
    }
}
