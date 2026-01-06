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

class ScrapingService
{
    protected PageFetchService $pageFetchService;
    protected DataIndexService $indexService;
    protected string $timezone = 'America/Denver';

    public function __construct(PageFetchService $pageFetchService, DataIndexService $indexService)
    {
        $this->pageFetchService = $pageFetchService;
        $this->indexService = $indexService;
    }

    public function scrapeBets(string $gameValue, string $url): int
    {
        try {
            $game = Game::from($gameValue);
            $type = GameDataType::Bets;

            $index = $this->indexService->start($gameValue . '_bets');
            $rowsUpdated = 0;

            $page = $this->pageFetchService->fetchPageContent($url);

            if (empty($page)) {
                $this->indexService->finish();
                return 0;
            }

            libxml_use_internal_errors(true);
            $dom = new DOMDocument();
            $loaded = @$dom->loadHTML($page);
            $errors = libxml_get_errors();
            libxml_clear_errors();

            if (!$loaded || !empty($errors)) {
                $this->indexService->finish();
                return 0;
            }

            $xpath = new DOMXPath($dom);
            $spreadCards = $xpath->query('//div[contains(@class,"consensus-table-spread")]');

            if ($spreadCards->length == 0) {
                $this->indexService->finish();
                return 0;
            }

            DB::beginTransaction();

            try {
                for ($i = 0; $i < $spreadCards->length; $i++) {
                    $card = $spreadCards->item($i);
                    if (!$card) {
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

                    if (empty($teamA) || empty($teamB)) {
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
                            continue;
                        }
                    }

                    if (empty($gameDate)) {
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

                    $rowsUpdated++;
                }

                DB::commit();
                $this->indexService->finish();
                return $rowsUpdated;
            } catch (\Exception $e) {
                DB::rollBack();
                $this->indexService->finish();
                throw $e;
            }
        } catch (\Throwable $e) {
            try {
                $this->indexService->finish();
            } catch (\Throwable $finishError) {
                // Ignore finish errors if index wasn't created
            }
            throw $e;
        }
    }

    public function scrapeResults(string $gameValue, string $url, ?string $date = null, ?string $week = null, ?string $year = null): int
    {
        try {
            $game = Game::from($gameValue);
            $type = GameDataType::Results;

            $index = $this->indexService->start($gameValue . '_results', $date, $week, $year);
            $rowsUpdated = 0;

            $page = $this->pageFetchService->fetchPageContent($url);

            if (empty($page)) {
                $this->indexService->finish();
                return 0;
            }

            libxml_use_internal_errors(true);
            $dom = new DOMDocument();
            $loaded = @$dom->loadHTML($page);
            $errors = libxml_get_errors();
            libxml_clear_errors();

            if (!$loaded || !empty($errors)) {
                $this->indexService->finish();
                return 0;
            }

            $xpath = new DOMXPath($dom);
            $eventCards = $xpath->query('//div[contains(@class,"event-card") and @data-time]');

            if ($eventCards->length == 0) {
                $this->indexService->finish();
                return 0;
            }

            DB::beginTransaction();

            try {
                foreach ($eventCards as $card) {
                    if (!$card) {
                        continue;
                    }

                    $cardXpath = new DOMXPath($dom);

                    $statusNode = $cardXpath->query('.//span[@data-field="state"]', $card)->item(0);
                    $status = $statusNode ? trim($statusNode->textContent) : '';

                    if (stripos($status, 'FINAL') === false) {
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
                            continue;
                        }
                    }

                    if (empty($gameDate)) {
                        continue;
                    }

                    $awayRow = $cardXpath->query('.//tr[@data-side="away"]', $card)->item(0);
                    $homeRow = $cardXpath->query('.//tr[@data-side="home"]', $card)->item(0);

                    if (!$awayRow || !$homeRow) {
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

                    if (empty($awayTeam) || empty($homeTeam)) {
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

                    $rowsUpdated++;
                }

                DB::commit();
                $this->indexService->finish();
                return $rowsUpdated;
            } catch (\Exception $e) {
                DB::rollBack();
                $this->indexService->finish();
                throw $e;
            }
        } catch (\Throwable $e) {
            try {
                $this->indexService->finish();
            } catch (\Throwable $finishError) {
                // Ignore finish errors if index wasn't created
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

