<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Enums\Game;
use App\Enums\GameDataType;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Http\JsonResponse;
use App\Models\DataIndex;

class DataController extends Controller
{
    public function getData(Game $game, GameDataType $type)
    {
        $model = $game->getModel($type);

        $data = $model::with('dataIndex')
            ->orderBy('data_index_id', 'desc')
            ->paginate(100);

        // group the paginator's collection by data_index_id for the view
        $groupedData = $data->getCollection()->groupBy('data_index_id');

        return view('data', [
            'game' => $game->value,
            'type' => $type->value,
            'groupedData' => $groupedData,
            'paginator' => $data,
        ]);
    }
    public function exportCsv(Request $request)
    {
        // Expect query params: game, type, indices[]
        $gameValue = $request->query('game');
        $typeValue = $request->query('type');

        if (empty($gameValue) || empty($typeValue)) {
            return redirect()->back()->with('error', 'Missing game or type for export');
        }

        try {
            $game = Game::from($gameValue);
            $type = GameDataType::from($typeValue);
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Invalid game or type');
        }

        $modelClass = $game->getModel($type);

        $indices = $request->query('indices', []);
        if (!is_array($indices)) {
            $indices = [$indices];
        }

        $query = $modelClass::query();
        if (!empty($indices)) {
            $query->whereIn('data_index_id', $indices);
        }

        $query->with('dataIndex');

        // prepare export path
        $exportDir = storage_path('app/exports');
        if (!is_dir($exportDir)) {
            mkdir($exportDir, 0755, true);
        }

        $filename = sprintf('%s_%s_%s.csv', $gameValue, $typeValue, date('Ymd_His'));
        $filePath = $exportDir . DIRECTORY_SEPARATOR . $filename;

        $handle = fopen($filePath, 'w');

        if ($typeValue === 'bets') {
            fputcsv($handle, ['Game Date', 'Game Time', 'Team Left', 'Team Right', 'Spread Left', 'Spread Right', 'Percentage Bets Left', 'Percentage Bets Right', 'Percentage Money Left', 'Percentage Money Right', 'Data Index']);

            $query->chunk(1000, function ($rows) use ($handle) {
                foreach ($rows as $row) {
                    fputcsv($handle, [
                        $row->game_date,
                        $row->game_time,
                        $row->team_left,
                        $row->team_right,
                        $row->spread_left,
                        $row->spread_right,
                        $row->perc_bets_left,
                        $row->perc_bets_right,
                        $row->perc_money_left,
                        $row->perc_money_right,
                        $row->data_index_id,
                    ]);
                }
            });
        } else {
            fputcsv($handle, ['Game Date', 'Game Time', 'Team Left', 'Team Right', 'Score Left', 'Score Right', 'Winning Spread', 'Data Index']);

            $query->chunk(1000, function ($rows) use ($handle) {
                foreach ($rows as $row) {
                    fputcsv($handle, [
                        $row->game_date,
                        $row->game_time,
                        $row->team_left,
                        $row->team_right,
                        $row->score_left,
                        $row->score_right,
                        $row->winning_spread,
                        $row->data_index_id,
                    ]);
                }
            });
        }

        fclose($handle);

        $gameDisplay = match($gameValue) {
            'nfl' => 'NFL',
            'ncaaf' => 'NCAAF',
            'ncaab' => 'NCAAB',
            default => ucfirst($gameValue),
        };

        $typeDisplay = ucfirst($typeValue);

        return view('export_download', [
            'file' => $filename,
            'path' => 'exports/' . $filename,
            'game' => $gameDisplay,
            'type' => $typeDisplay,
        ]);
    }

    public function downloadExport($file)
    {
        $file = basename($file);
        $path = storage_path('app/exports/' . $file);
        if (!file_exists($path)) {
            abort(404);
        }

        return response()->download($path, $file, ['Content-Type' => 'text/csv']);
    }

    public function getIndices(Request $request): JsonResponse
    {
        $game = $request->query('game');
        $type = $request->query('type');

        if (empty($game) || empty($type)) {
            return response()->json(['error' => 'Missing game or type'], 400);
        }

        try {
            $gameEnum = Game::from($game);
            $typeEnum = GameDataType::from($type);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Invalid game or type'], 400);
        }

        $modelClass = $gameEnum->getModel($typeEnum);
        $scraperName = sprintf('%s_%s', $game, $type);

        $indices = DataIndex::where('scraper_name', $scraperName)
            ->orderBy('id', 'desc')
            ->get(['id', 'scraped_at', 'rows_found', 'rows_merged', 'date', 'week', 'year']);

        $indicesWithCounts = $indices->map(function ($index) use ($modelClass) {
            $actualCount = $modelClass::where('data_index_id', $index->id)->count();
            $index->actual_rows = $actualCount;
            return $index;
        });

        return response()->json($indicesWithCounts);
    }
}
