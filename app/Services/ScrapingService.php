<?php

namespace App\Services;

use App\Enums\Game;
use App\Enums\GameDataType;
use App\Models\DataIndex;
use DOMDocument;
use DOMXPath;
use DateTime;
use DateTimeZone;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ScrapingService
{
    protected PageFetchService $pageFetchService;
    protected DataIndexService $indexService;
    protected string $timezone = 'America/Denver';
    protected bool $debug = true;

    public function __construct(PageFetchService $pageFetchService, DataIndexService $indexService)
    {
        $this->pageFetchService = $pageFetchService;
        $this->indexService = $indexService;
    }

    protected function log(string $message, array $context = [], string $level = 'info'): void
    {
        if (!$this->debug) {
            return;
        }
        
        $prefixed = "[ScrapingService] " . $message;
        match ($level) {
            'error' => Log::error($prefixed, $context),
            'warning' => Log::warning($prefixed, $context),
            'debug' => Log::debug($prefixed, $context),
            default => Log::info($prefixed, $context),
        };
    }

    public function scrapeBets(string $gameValue, string $url): int
    {
        $this->log("=== Starting scrapeBets ===", ['game' => $gameValue, 'url' => $url]);
        
        try {
            $game = Game::from($gameValue);
            $type = GameDataType::Bets;
            $this->log("Game enum resolved", ['game' => $game->value, 'type' => $type->value]);

            $index = $this->indexService->start($gameValue . '_bets');
            $this->log("Index started", ['index_id' => $index->id]);
            $rowsUpdated = 0;

            $page = $this->pageFetchService->fetchPageContent($url);
            $pageLength = strlen($page ?? '');
            $this->log("Page fetched", ['length' => $pageLength]);

            if (empty($page)) {
                $this->log("EXIT: Empty page content", [], 'warning');
                $this->indexService->finish();
                return 0;
            }

            $this->log("HTML preview (first 500 chars)", ['html' => substr($page, 0, 500)]);

            libxml_use_internal_errors(true);
            $dom = new DOMDocument();
            $loaded = @$dom->loadHTML($page);
            $errors = libxml_get_errors();
            libxml_clear_errors();

            $this->log("DOM loaded", [
                'loaded' => $loaded,
                'error_count' => count($errors),
                'errors' => array_map(fn($e) => $e->message, array_slice($errors, 0, 5))
            ]);

            if (!$loaded) {
                $this->log("EXIT: DOM failed to load", [], 'error');
                $this->indexService->finish();
                return 0;
            }

            $xpath = new DOMXPath($dom);
            $spreadCards = $xpath->query('//div[contains(@class,"consensus-table-spread")]');
            $this->log("Spread cards found", ['count' => $spreadCards->length]);

            if ($spreadCards->length == 0) {
                $allDivs = $xpath->query('//div[@class]');
                $classNames = [];
                for ($i = 0; $i < min(20, $allDivs->length); $i++) {
                    $div = $allDivs->item($i);
                    if ($div instanceof \DOMElement) {
                        $classNames[] = $div->getAttribute('class');
                    }
                }
                $this->log("EXIT: No spread cards. Sample div classes", ['classes' => $classNames], 'warning');
                $this->indexService->finish();
                return 0;
            }

            DB::beginTransaction();
            $this->log("DB transaction started");

            try {
                $skippedReasons = [];
                
                for ($i = 0; $i < $spreadCards->length; $i++) {
                    $card = $spreadCards->item($i);
                    if (!$card) {
                        $skippedReasons[] = "Card $i: null card";
                        continue;
                    }

                    $cardXpath = new DOMXPath($dom);

                    $leftTeamNode = $cardXpath->query('.//div[contains(@class,"team-pennant left")]//span[@class="team-name"]/span', $card);
                    $rightTeamNode = $cardXpath->query('.//div[contains(@class,"team-pennant right")]//span[@class="team-name"]/span', $card);
                    $timeNode = $cardXpath->query('.//span[@data-role="localtime"]/@data-value', $card);
                    $percBetsNodes = $cardXpath->query('.//span[@class="trend-graph-percentage"][1]//span[contains(@class,"percentage")]', $card);
                    $percMoneyNodes = $cardXpath->query('.//span[@class="trend-graph-percentage"][@style and contains(@style,"background-color:#fff")]//span[contains(@class,"percentage")]', $card);
                    $strongNodes = $cardXpath->query('.//strong', $card);

                    $teamA = $leftTeamNode->length > 0 && $leftTeamNode->item(0) ? trim($leftTeamNode->item(0)->nodeValue) : '';
                    $teamB = $rightTeamNode->length > 0 && $rightTeamNode->item(0) ? trim($rightTeamNode->item(0)->nodeValue) : '';
                    $datetime = $timeNode->length > 0 && $timeNode->item(0) ? trim($timeNode->item(0)->nodeValue) : '';

                    $this->log("Card $i parsed", [
                        'teamA' => $teamA,
                        'teamB' => $teamB,
                        'datetime' => $datetime,
                        'leftTeamNodeCount' => $leftTeamNode->length,
                        'rightTeamNodeCount' => $rightTeamNode->length,
                    ], 'debug');

                    if (empty($teamA) || empty($teamB)) {
                        $skippedReasons[] = "Card $i: Empty teams (A='$teamA', B='$teamB')";
                        continue;
                    }

                    $percentABets = $percBetsNodes->length > 0 && $percBetsNodes->item(0) ? trim($percBetsNodes->item(0)->nodeValue) : '';
                    $percentBBets = $percBetsNodes->length > 1 && $percBetsNodes->item(1) ? trim($percBetsNodes->item(1)->nodeValue) : '';
                    $percentAMoney = $percMoneyNodes->length > 0 && $percMoneyNodes->item(0) ? trim($percMoneyNodes->item(0)->nodeValue) : '';
                    $percentBMoney = $percMoneyNodes->length > 1 && $percMoneyNodes->item(1) ? trim($percMoneyNodes->item(1)->nodeValue) : '';

                    $spreadA = $strongNodes->length > 0 && $strongNodes->item(0) ? preg_replace('/\s+/', ' ', trim($strongNodes->item(0)->nodeValue)) : '';
                    $spreadB = $strongNodes->length > 1 && $strongNodes->item(1) ? preg_replace('/\s+/', ' ', trim($strongNodes->item(1)->nodeValue)) : '';

                    $gameDate = '';
                    $gameTime = '';
                    if (!empty($datetime) && strlen($datetime) >= 16) {
                        $gameDate = substr($datetime, 0, 10);
                        $gameTime = substr($datetime, 11, 5);
                    } elseif (!empty($datetime)) {
                        try {
                            $dt = new DateTime($datetime);
                            $dt->setTimezone(new DateTimeZone($this->timezone));
                            $gameDate = $dt->format('Y-m-d');
                            $gameTime = $dt->format('H:i');
                        } catch (\Exception $e) {
                            $skippedReasons[] = "Card $i: DateTime parse error - " . $e->getMessage();
                            continue;
                        }
                    }

                    if (empty($gameDate)) {
                        $skippedReasons[] = "Card $i: Empty game date (datetime='$datetime')";
                        continue;
                    }

                    $spreadLeft = $this->parseFloat($spreadA);
                    $spreadRight = $this->parseFloat($spreadB);
                    $percBetsLeft = $this->parsePercentage($percentABets);
                    $percBetsRight = $this->parsePercentage($percentBBets);
                    $percMoneyLeft = $this->parsePercentage($percentAMoney);
                    $percMoneyRight = $this->parsePercentage($percentBMoney);

                    $model = $game->getModel($type);
                    $this->indexService->incrementFound();

                    $data = [
                        'game_date' => $gameDate,
                        'game_time' => $gameTime,
                        'team_left' => $teamA,
                        'team_right' => $teamB,
                        'spread_left' => $spreadLeft,
                        'spread_right' => $spreadRight,
                        'perc_bets_left' => $percBetsLeft,
                        'perc_bets_right' => $percBetsRight,
                        'perc_money_left' => $percMoneyLeft,
                        'perc_money_right' => $percMoneyRight,
                        'data_index_id' => $index->id,
                    ];
                    
                    $this->log("Attempting updateOrCreate", ['model' => $model, 'data' => $data], 'debug');

                    try {
                        $model::updateOrCreate(
                            [
                                'game_date' => $gameDate,
                                'game_time' => $gameTime,
                                'team_left' => $teamA,
                                'team_right' => $teamB,
                            ],
                            [
                                'spread_left' => $spreadLeft,
                                'spread_right' => $spreadRight,
                                'perc_bets_left' => $percBetsLeft,
                                'perc_bets_right' => $percBetsRight,
                                'perc_money_left' => $percMoneyLeft,
                                'perc_money_right' => $percMoneyRight,
                                'data_index_id' => $index->id,
                            ]
                        );
                        $this->log("Row saved successfully", ['teams' => "$teamA vs $teamB"]);
                    } catch (\Exception $e) {
                        $this->log("updateOrCreate failed", [
                            'error' => $e->getMessage(),
                            'data' => $data
                        ], 'error');
                        throw $e;
                    }

                    $rowsUpdated++;
                }

                if (!empty($skippedReasons)) {
                    $this->log("Skipped cards summary", ['reasons' => $skippedReasons], 'warning');
                }

                DB::commit();
                $this->log("Transaction committed", ['rows_updated' => $rowsUpdated]);
                $this->indexService->finish();
                return $rowsUpdated;
            } catch (\Exception $e) {
                DB::rollBack();
                $this->log("Transaction rolled back", ['error' => $e->getMessage()], 'error');
                $this->indexService->finish();
                throw $e;
            }
        } catch (\Throwable $e) {
            $this->log("FATAL ERROR in scrapeBets", [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => array_slice($e->getTrace(), 0, 5)
            ], 'error');
            try {
                $this->indexService->finish();
            } catch (\Throwable $finishError) {
            }
            throw $e;
        }
    }

    public function scrapeResults(string $gameValue, string $url, ?string $date = null, ?string $week = null, ?string $year = null): int
    {
        $this->log("=== Starting scrapeResults ===", [
            'game' => $gameValue, 
            'url' => $url,
            'date' => $date,
            'week' => $week,
            'year' => $year
        ]);
        
        try {
            $game = Game::from($gameValue);
            $type = GameDataType::Results;
            $this->log("Game enum resolved", ['game' => $game->value, 'type' => $type->value]);

            $index = $this->indexService->start($gameValue . '_results', $date, $week, $year);
            $this->log("Index started", ['index_id' => $index->id]);
            $rowsUpdated = 0;

            $page = $this->pageFetchService->fetchPageContent($url);
            $pageLength = strlen($page ?? '');
            $this->log("Page fetched", ['length' => $pageLength]);

            if (empty($page)) {
                $this->log("EXIT: Empty page content", [], 'warning');
                $this->indexService->finish();
                return 0;
            }

            $this->log("HTML preview (first 500 chars)", ['html' => substr($page, 0, 500)]);

            libxml_use_internal_errors(true);
            $dom = new DOMDocument();
            $loaded = @$dom->loadHTML($page);
            $errors = libxml_get_errors();
            libxml_clear_errors();

            $this->log("DOM loaded", [
                'loaded' => $loaded,
                'error_count' => count($errors),
                'errors' => array_map(fn($e) => $e->message, array_slice($errors, 0, 5))
            ]);

            if (!$loaded) {
                $this->log("EXIT: DOM failed to load", [], 'error');
                $this->indexService->finish();
                return 0;
            }

            $xpath = new DOMXPath($dom);
            $eventCards = $xpath->query('//div[contains(@class,"event-card") and @data-time]');
            $this->log("Event cards found", ['count' => $eventCards->length]);

            if ($eventCards->length == 0) {
                $allDivs = $xpath->query('//div[@class]');
                $classNames = [];
                for ($i = 0; $i < min(20, $allDivs->length); $i++) {
                    $div = $allDivs->item($i);
                    if ($div instanceof \DOMElement) {
                        $classNames[] = $div->getAttribute('class');
                    }
                }
                $this->log("EXIT: No event cards. Sample div classes", ['classes' => $classNames], 'warning');
                $this->indexService->finish();
                return 0;
            }

            DB::beginTransaction();
            $this->log("DB transaction started");

            try {
                $skippedReasons = [];
                $cardIndex = 0;
                
                foreach ($eventCards as $card) {
                    if (!$card) {
                        $skippedReasons[] = "Card $cardIndex: null card";
                        $cardIndex++;
                        continue;
                    }

                    $cardXpath = new DOMXPath($dom);

                    $statusNode = $cardXpath->query('.//span[@data-field="state"]', $card)->item(0);
                    $status = $statusNode ? trim($statusNode->textContent) : '';

                    $this->log("Card $cardIndex status check", ['status' => $status], 'debug');

                    if (stripos($status, 'FINAL') === false) {
                        $skippedReasons[] = "Card $cardIndex: Status not FINAL ('$status')";
                        $cardIndex++;
                        continue;
                    }

                    $timeNode = $cardXpath->query('.//span[@data-role="localtime"]/@data-value', $card)->item(0);
                    $datetime = $timeNode ? trim($timeNode->nodeValue) : '';

                    $gameDate = '';
                    $gameTime = '';
                    if (!empty($datetime)) {
                        try {
                            $dt = new DateTime($datetime);
                            $dt->setTimezone(new DateTimeZone($this->timezone));
                            $gameDate = $dt->format('Y-m-d');
                            $gameTime = $dt->format('H:i');
                        } catch (\Exception $e) {
                            $skippedReasons[] = "Card $cardIndex: DateTime parse error - " . $e->getMessage();
                            $cardIndex++;
                            continue;
                        }
                    }

                    if (empty($gameDate)) {
                        $skippedReasons[] = "Card $cardIndex: Empty game date (datetime='$datetime')";
                        $cardIndex++;
                        continue;
                    }

                    $awayRow = $cardXpath->query('.//tr[@data-side="away"]', $card)->item(0);
                    $homeRow = $cardXpath->query('.//tr[@data-side="home"]', $card)->item(0);

                    if (!$awayRow || !$homeRow) {
                        $skippedReasons[] = "Card $cardIndex: Missing away/home rows";
                        $cardIndex++;
                        continue;
                    }

                    $awayTeamNode = $cardXpath->query('.//span[@class="team-name"]//span', $awayRow)->item(0);
                    $awayTeam = $awayTeamNode ? trim($awayTeamNode->textContent) : '';

                    $awayScoreNode = $cardXpath->query('.//td[contains(@class,"event-card-score")]', $awayRow)->item(0);
                    $awayScore = $awayScoreNode ? trim($awayScoreNode->textContent) : null;

                    $homeTeamNode = $cardXpath->query('.//span[@class="team-name"]//span', $homeRow)->item(0);
                    $homeTeam = $homeTeamNode ? trim($homeTeamNode->textContent) : '';

                    $homeScoreNode = $cardXpath->query('.//td[contains(@class,"event-card-score")]', $homeRow)->item(0);
                    $homeScore = $homeScoreNode ? trim($homeScoreNode->textContent) : null;

                    $this->log("Card $cardIndex parsed", [
                        'awayTeam' => $awayTeam,
                        'homeTeam' => $homeTeam,
                        'awayScore' => $awayScore,
                        'homeScore' => $homeScore,
                        'datetime' => $datetime,
                    ], 'debug');

                    if (empty($awayTeam) || empty($homeTeam)) {
                        $skippedReasons[] = "Card $cardIndex: Empty teams (away='$awayTeam', home='$homeTeam')";
                        $cardIndex++;
                        continue;
                    }

                    $awayScore = $this->parseScore($awayScore);
                    $homeScore = $this->parseScore($homeScore);

                    $winningSpreadNodeAway = $cardXpath->query('(.//td[@data-field="live-spread" and contains(@class,"win")]/*)[1]', $awayRow)->item(0);
                    $winningSpreadNodeHome = $cardXpath->query('(.//td[@data-field="live-spread" and contains(@class,"win")]/*)[1]', $homeRow)->item(0);

                    $winningSpread = null;
                    foreach ([$winningSpreadNodeAway, $winningSpreadNodeHome] as $node) {
                        if ($node && trim($node->textContent) !== '') {
                            $winningSpread = trim($node->textContent);
                            break;
                        }
                    }

                    if ($winningSpread === null) {
                        $winningSpread = '0';
                    }

                    $model = $game->getModel($type);
                    $this->indexService->incrementFound();

                    $data = [
                        'game_date' => $gameDate,
                        'game_time' => $gameTime,
                        'team_left' => $awayTeam,
                        'team_right' => $homeTeam,
                        'score_left' => $awayScore,
                        'score_right' => $homeScore,
                        'winning_spread' => $winningSpread,
                        'data_index_id' => $index->id,
                    ];

                    $this->log("Attempting updateOrCreate", ['model' => $model, 'data' => $data], 'debug');

                    try {
                        $model::updateOrCreate(
                            [
                                'game_date' => $gameDate,
                                'game_time' => $gameTime,
                                'team_left' => $awayTeam,
                                'team_right' => $homeTeam,
                            ],
                            [
                                'score_left' => $awayScore,
                                'score_right' => $homeScore,
                                'winning_spread' => $winningSpread,
                                'data_index_id' => $index->id,
                            ]
                        );
                        $this->log("Row saved successfully", ['teams' => "$awayTeam vs $homeTeam"]);
                    } catch (\Exception $e) {
                        $this->log("updateOrCreate failed", [
                            'error' => $e->getMessage(),
                            'data' => $data
                        ], 'error');
                        throw $e;
                    }

                    $rowsUpdated++;
                    $cardIndex++;
                }

                if (!empty($skippedReasons)) {
                    $this->log("Skipped cards summary", ['reasons' => $skippedReasons], 'warning');
                }

                DB::commit();
                $this->log("Transaction committed", ['rows_updated' => $rowsUpdated]);
                $this->indexService->finish();
                return $rowsUpdated;
            } catch (\Exception $e) {
                DB::rollBack();
                $this->log("Transaction rolled back", ['error' => $e->getMessage()], 'error');
                $this->indexService->finish();
                throw $e;
            }
        } catch (\Throwable $e) {
            $this->log("FATAL ERROR in scrapeResults", [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => array_slice($e->getTrace(), 0, 5)
            ], 'error');
            try {
                $this->indexService->finish();
            } catch (\Throwable $finishError) {
            }
            throw $e;
        }
    }

    protected function parseFloat(?string $value): ?float
    {
        if (empty($value)) {
            return null;
        }

        $cleaned = preg_replace('/[^\d\.\-\+]/', '', $value);
        if ($cleaned === '' || $cleaned === '-') {
            return null;
        }

        $float = (float) $cleaned;
        return is_finite($float) ? $float : null;
    }

    protected function parsePercentage(?string $value): ?int
    {
        if (empty($value)) {
            return null;
        }

        $cleaned = rtrim(trim($value), '%');
        $cleaned = preg_replace('/[^\d]/', '', $cleaned);

        if ($cleaned === '') {
            return null;
        }

        $int = (int) $cleaned;
        return ($int >= 0 && $int <= 100) ? $int : null;
    }

    protected function parseScore(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $trimmed = trim($value);
        if ($trimmed === '' || !is_numeric($trimmed)) {
            return null;
        }

        return $trimmed;
    }
}
