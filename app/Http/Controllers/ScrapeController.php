<?php

namespace App\Http\Controllers;

use App\Services\PageFetchService;
use Illuminate\Http\Request;
use DOMDocument;
use DOMXPath;
use App\Models\NFLBets;
use App\Models\NFLResults;
use DateTime;
use DateTimeZone;

class ScrapeController extends Controller
{
    public function scrapeBets(Request $request, PageFetchService $pageFetchService)
    {
        echo $request['game'];
        //Hardcoded for testing purposes
        $url = "https://www.scoresandodds.com/" . $request['game'] . "/consensus-picks";
        $page = $pageFetchService->fetchPageContent($url);

        if (!$page) {
            echo "Error: No page content received\n";
            return;
        }

        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        $dom->loadHTML($page);
        libxml_clear_errors();

        $xpath = new DOMXPath($dom);

        // Get all SPREAD card containers
        $spreadCards = $xpath->query('//div[contains(@class,"consensus-table-spread")]');

        if ($spreadCards->length == 0) {
            echo "Warning: No games found. The website may use JavaScript rendering.\n";
            return;
        }

        for ($i = 0; $i < $spreadCards->length; $i++) {
            $card = $spreadCards->item($i);

            // Query within each card context
            $cardXpath = new DOMXPath($dom);

            // Get teams from this card
            $leftTeamNode = $cardXpath->query('.//div[contains(@class,"team-pennant left")]//span[@class="team-name"]/span', $card);
            $rightTeamNode = $cardXpath->query('.//div[contains(@class,"team-pennant right")]//span[@class="team-name"]/span', $card);

            // Get datetime from this card
            $timeNode = $cardXpath->query('.//span[@data-role="localtime"]/@data-value', $card);

            // Get percentages from this card - % of Bets (first trend-graph-percentage bar)
            $percBetsNodes = $cardXpath->query('.//span[@class="trend-graph-percentage"][1]//span[contains(@class,"percentage")]', $card);

            // Get percentages from this card - % of Money (second trend-graph-percentage bar with white background)
            $percMoneyNodes = $cardXpath->query('.//span[@class="trend-graph-percentage"][@style and contains(@style,"background-color:#fff")]//span[contains(@class,"percentage")]', $card);

            // Get spreads from this card
            $strongNodes = $cardXpath->query('.//strong', $card);

            // Extract values
            $teamA = trim($leftTeamNode->length > 0 ? $leftTeamNode->item(0)->nodeValue : '');
            $teamB = trim($rightTeamNode->length > 0 ? $rightTeamNode->item(0)->nodeValue : '');
            $datetime = trim($timeNode->length > 0 ? $timeNode->item(0)->nodeValue : '');

            // % of Bets percentages
            $percentABets = trim($percBetsNodes->length > 0 ? $percBetsNodes->item(0)->nodeValue : '');
            $percentBBets = trim($percBetsNodes->length > 1 ? $percBetsNodes->item(1)->nodeValue : '');

            // % of Money percentages
            $percentAMoney = trim($percMoneyNodes->length > 0 ? $percMoneyNodes->item(0)->nodeValue : '');
            $percentBMoney = trim($percMoneyNodes->length > 1 ? $percMoneyNodes->item(1)->nodeValue : '');

            // Extract spreads (first 2 <strong> tags in this card)
            $spreadA = preg_replace('/\s+/', ' ', trim($strongNodes->length > 0 ? $strongNodes->item(0)->nodeValue : ''));
            $spreadB = preg_replace('/\s+/', ' ', trim($strongNodes->length > 1 ? $strongNodes->item(1)->nodeValue : ''));

            // Save to database
            NFLBets::updateOrCreate([
                'game_date' => substr($datetime, 0, 10),
                'game_time' => substr($datetime, 11),
                'team_left' => $teamA,
                'team_right' => $teamB,
                'spread_left' => floatval($spreadA),
                'spread_right' => floatval($spreadB),
                'perc_bets_left' => rtrim($percentABets, '%'),
                'perc_bets_right' => rtrim($percentBBets, '%'),
                'perc_money_left' => rtrim($percentAMoney, '%'),
                'perc_money_right' => rtrim($percentBMoney, '%'),
            ]);
            // fputcsv($csvFile, [$teamA, $teamB, $datetime, $percentABets, $percentBBets, $percentAMoney, $percentBMoney, $spreadA, $spreadB]);
        }

        // file_put_contents(storage_path($request['game'] . ".html"), $page);
        echo NFLBets::all();
        return "Scraping completed and data saved.\n";
    }


    public function scrapeResults(Request $request, PageFetchService $pageFetchService)
    {
        echo $request['game'];
        //Hardcoded for testing purposes
        $url = "https://www.scoresandodds.com/" . $request['game'];
        $page = $pageFetchService->fetchPageContent($url);

        if (!$page) {
            echo "Error: No page content received\n";
            return;
        }

        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        $dom->loadHTML($page);
        libxml_clear_errors();

        $xpath = new DOMXPath($dom);

        $eventCards = $xpath->query('//div[contains(@class,"event-card") and @data-time]');

        if ($eventCards->length == 0) {
            return [];
        }

        $games = [];

        foreach ($eventCards as $card) {
            $cardXpath = new DOMXPath($dom);

            $statusNode = $cardXpath->query('.//span[@data-field="state"]', $card)->item(0);
            $status = $statusNode ? trim($statusNode->textContent) : '';

            if (stripos($status, 'FINAL') === false) {
                continue;
            }

            $timeNode = $cardXpath->query('.//span[@data-role="localtime"]/@data-value', $card)->item(0);
            $datetime = $timeNode ? trim($timeNode->nodeValue) : '';

            $date = '';
            $time = '';
            if ($datetime) {
                $dt = new DateTime($datetime);
                $dt->setTimezone(new DateTimeZone('America/Denver'));
                $date = $dt->format('Y-m-d');
                $time = $dt->format('H:i');
            }

            $awayRow = $cardXpath->query('.//tr[@data-side="away"]', $card)->item(0);
            $homeRow = $cardXpath->query('.//tr[@data-side="home"]', $card)->item(0);

            if (!$awayRow || !$homeRow) {
                continue;
            }

            $awayTeamNode = $cardXpath->query('.//span[@class="team-name"]//span', $awayRow)->item(0);
            $awayTeam = $awayTeamNode ? trim($awayTeamNode->textContent) : '';

            $awayScoreNode = $cardXpath->query('.//td[contains(@class,"event-card-score")]', $awayRow)->item(0);
            $awayScore = $awayScoreNode ? trim($awayScoreNode->textContent) : '0';

            $homeTeamNode = $cardXpath->query('.//span[@class="team-name"]//span', $homeRow)->item(0);
            $homeTeam = $homeTeamNode ? trim($homeTeamNode->textContent) : '';

            $homeScoreNode = $cardXpath->query('.//td[contains(@class,"event-card-score")]', $homeRow)->item(0);
            $homeScore = $homeScoreNode ? trim($homeScoreNode->textContent) : '0';

            $winningSpread = '';
            $awaySpreadTd = $cardXpath->query('.//td[@data-field="live-spread" and @data-side="away" and contains(@class,"win")]', $awayRow)->item(0);
            $homeSpreadTd = $cardXpath->query('.//td[@data-field="live-spread" and @data-side="home" and contains(@class,"win")]', $homeRow)->item(0);

            $winningSide = null; // 'away' or 'home'
            if ($awaySpreadTd) {
                $spreadValueNode = $cardXpath->query('.//span[@class="data-value"]', $awaySpreadTd)->item(0);
                if ($spreadValueNode) {
                    $winningSpread = trim($spreadValueNode->textContent);
                    $winningSide = 'away';
                }
            } elseif ($homeSpreadTd) {
                $spreadValueNode = $cardXpath->query('.//span[@class="data-value"]', $homeSpreadTd)->item(0);
                if ($spreadValueNode) {
                    $winningSpread = trim($spreadValueNode->textContent);
                    $winningSide = 'home';
                }
            }

            // Persist to nfl_results table using NFLResults model
            $winningSpreadFloat = null;
            if ($winningSpread !== '') {
                $normalized = str_replace('+', '', $winningSpread);
                if (is_numeric($normalized)) {
                    $winningSpreadFloat = floatval($normalized);
                }
            }

            $attributes = [
                'game_date' => $date,
                'game_time' => $time,
                'team_left' => $awayTeam,
                'team_right' => $homeTeam,
            ];

            $values = [
                // reset spreads to null and set the winning side spread when available
                'spread_left' => $winningSide === 'away' ? $winningSpreadFloat : null,
                'spread_right' => $winningSide === 'home' ? $winningSpreadFloat : null,
                'perc_bets_left' => null,
                'perc_bets_right' => null,
                'perc_money_left' => null,
                'perc_money_right' => null,
            ];

            NFLResults::updateOrCreate($attributes, $values);

            $games[] = [$awayTeam, $awayScore, $homeTeam, $homeScore, $date, $time, $winningSpread];
        }

        return $games;
    }
}
