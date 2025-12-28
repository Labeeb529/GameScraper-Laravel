<?php

namespace App\Http\Controllers;

use App\Models\ScraperJobs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SchedulingController extends Controller
{
    public function index()
    {
        $regularJobs = ScraperJobs::where('run_once', false)->orderBy('created_at', 'desc')->get();
        $onetimeJobs = ScraperJobs::where('run_once', true)->orderBy('created_at', 'desc')->get();

        return view('cron', [
            'regularJobs' => $regularJobs,
            'onetimeJobs' => $onetimeJobs,
        ]);
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'scraper_type' => 'required|string|in:nfl-bets,nfl-results,ncaaf-bets,ncaaf-results,ncaab-bets,ncaab-results',
            'schedule_type' => 'required|string|in:regular,onetime',
            'time' => 'required|date_format:H:i',
            'run_once' => 'required|boolean',
        ];

        if ($request->schedule_type === 'onetime') {
            $rules['run_date'] = 'required|date|after_or_equal:today';
        } else {
            $rules['day_of_week'] = 'required|string';
        }

        $scraperType = $request->scraper_type ?? '';
        if (str_contains($scraperType, '-results')) {
            $game = str_replace('-results', '', $scraperType);
            if ($game === 'ncaab') {
                $rules['date'] = 'required|date';
            } else {
                $rules['year'] = 'required|integer|min:2020|max:' . (date('Y') + 1);
                $rules['week'] = 'required|integer|min:1|max:18';
            }
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $cronExpression = $this->buildCronExpression($request);
        if (empty($cronExpression)) {
            return redirect()->back()
                ->withErrors(['cron' => 'Invalid cron expression generated'])
                ->withInput();
        }

        $url = $this->buildScraperUrl($request);
        if (empty($url) || !filter_var($url, FILTER_VALIDATE_URL)) {
            return redirect()->back()
                ->withErrors(['url' => 'Invalid URL generated'])
                ->withInput();
        }

        ScraperJobs::create([
            'name' => $request->name,
            'scraper_type' => $request->scraper_type,
            'url' => $url,
            'cron_expression' => $cronExpression,
            'active' => true,
            'run_once' => $request->run_once,
        ]);

        return redirect()->route('scraper-jobs.index')
            ->with('success', 'Scraper job scheduled successfully!');
    }

    public function toggle($id)
    {
        $job = ScraperJobs::findOrFail($id);
        $job->update([
            'active' => !$job->active,
        ]);

        return redirect()->route('scraper-jobs.index')
            ->with('success', 'Job status updated successfully!');
    }

    public function destroy($id)
    {
        $job = ScraperJobs::findOrFail($id);
        $job->delete();

        return redirect()->route('scraper-jobs.index')
            ->with('success', 'Job deleted successfully!');
    }

    private function buildCronExpression(Request $request): string
    {
        $time = $request->time ?? '00:00';
        if (!preg_match('/^(\d{1,2}):(\d{1,2})$/', $time, $matches)) {
            return '';
        }

        $hour = (int) $matches[1];
        $minute = (int) $matches[2];

        if ($hour < 0 || $hour > 23 || $minute < 0 || $minute > 59) {
            return '';
        }

        if ($request->schedule_type === 'onetime') {
            $runDate = $request->run_date ?? null;
            if (!$runDate) {
                return '';
            }

            try {
                $date = new \DateTime($runDate);
                $day = (int) $date->format('j');
                $month = (int) $date->format('n');

                if ($day < 1 || $day > 31 || $month < 1 || $month > 12) {
                    return '';
                }

                return sprintf('%d %d %d %d *', $minute, $hour, $day, $month);
            } catch (\Exception $e) {
                return '';
            }
        } else {
            $dayOfWeek = $request->day_of_week ?? '*';
            if ($dayOfWeek !== '*' && (!is_numeric($dayOfWeek) || (int) $dayOfWeek < 0 || (int) $dayOfWeek > 6)) {
                return '';
            }

            return sprintf('%d %d * * %s', $minute, $hour, $dayOfWeek);
        }
    }

    private function buildScraperUrl(Request $request): string
    {
        $scraperType = $request->scraper_type ?? '';
        $baseUrl = 'https://www.scoresandodds.com/';

        if (str_contains($scraperType, '-bets')) {
            $game = str_replace('-bets', '', $scraperType);
            if (in_array($game, ['nfl', 'ncaaf', 'ncaab'])) {
                return $baseUrl . $game . '/consensus-picks';
            }
        }

        if (str_contains($scraperType, '-results')) {
            $game = str_replace('-results', '', $scraperType);

            if ($game === 'ncaab') {
                $date = $request->date ?? null;
                if ($date) {
                    return $baseUrl . $game . '?date=' . urlencode($date);
                }
            } elseif (in_array($game, ['nfl', 'ncaaf'])) {
                $year = $request->year ?? null;
                $week = $request->week ?? null;
                if ($year && $week) {
                    return $baseUrl . $game . '?week=' . urlencode($year) . '-reg-' . urlencode($week);
                }
            }
        }

        return '';
    }
}
